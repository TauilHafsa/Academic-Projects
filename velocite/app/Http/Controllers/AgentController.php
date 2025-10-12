<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rental;
use App\Models\User;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\RentalComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\View;

class AgentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:agent']);
    }

    /**
     * Display a list of all rentals for communication management.
     */
    public function rentals(Request $request): View
    {
        $user = $request->user();
        $city = $user->profile ? $user->profile->city : null;

        $status = $request->input('status', 'all');
        $rentalQuery = Rental::with(['bike', 'renter', 'bike.owner', 'comments']);

        // Filter by city if agent has one assigned
        if ($city) {
            $rentalQuery->whereHas('bike.owner.profile', function ($query) use ($city) {
                $query->where('city', $city);
            });
        }

        // Filter by status if specified
        if ($status !== 'all') {
            $rentalQuery->where('status', $status);
        }

        // Filter by search term if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $rentalQuery->where(function ($query) use ($search) {
                $query->whereHas('bike', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('renter', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('bike.owner', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $rentals = $rentalQuery->latest()->paginate(10);

        return view('agent.rentals', [
            'rentals' => $rentals,
            'status' => $status,
            'city' => $city,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Display detailed rental information with communication history.
     */
    public function showRental(string $id): View
    {
        $rental = Rental::with([
            'bike',
            'renter',
            'bike.owner',
            'comments.user',
            'bikeRating',
            'userRatings'
        ])->findOrFail($id);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to view rentals outside your assigned city.');
            }
        }

        // Get all comments - agents can see everything including private comments
        $comments = $rental->comments()->orderBy('created_at')->get();

        return view('agent.rental-detail', [
            'rental' => $rental,
            'comments' => $comments
        ]);
    }

    /**
     * Show the form for adding a comment as an agent.
     */
    public function createComment(string $rentalId): View
    {
        $rental = Rental::with(['bike', 'renter', 'bike.owner'])->findOrFail($rentalId);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to add comments to rentals outside your assigned city.');
            }
        }

        return view('agent.create-comment', compact('rental'));
    }

    /**
     * Store a new comment as an agent.
     */
    public function storeComment(Request $request, string $rentalId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'is_private' => 'nullable|boolean',
            'visible_to' => 'required|in:both,client,partner',
        ]);

        $rental = Rental::with(['bike', 'renter', 'bike.owner'])->findOrFail($rentalId);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to add comments to rentals outside your assigned city.');
            }
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create the comment
            $comment = $rental->comments()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
                'is_private' => $request->has('is_private') && $request->is_private ? true : false,
                'agent_comment' => true,
                'agent_comment_visibility' => $request->visible_to,
            ]);

            // Create notifications based on visibility setting
            if ($request->visible_to === 'both' || $request->visible_to === 'client') {
                $this->createCommentNotification($comment, $rental->renter_id, $rental);
            }

            if ($request->visible_to === 'both' || $request->visible_to === 'partner') {
                $this->createCommentNotification($comment, $rental->bike->owner_id, $rental);
            }

            DB::commit();

            return redirect()->route('agent.rental.show', $rental->id)
                ->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while adding your comment: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to create comment notifications.
     */
    private function createCommentNotification($comment, $userId, $rental)
    {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->type = 'agent_comment';
        $notification->notifiable_id = $comment->id;
        $notification->notifiable_type = get_class($comment);
        $notification->content = Auth::user()->name . ' (Support Agent) has added a comment to your rental #' . $rental->id;
        $notification->is_read = false;

        // Determine correct rental show route based on user role
        $user = User::find($userId);
        $notification->link = $user->hasRole('partner')
            ? route('partner.rentals.show', $rental->id)
            : route('rentals.show', $rental->id);

        $notification->save();
    }

    /**
     * Generate and send an evaluation form.
     */
    public function createEvaluationForm(string $rentalId): View
    {
        $rental = Rental::with(['bike', 'renter', 'bike.owner'])->findOrFail($rentalId);

        return view('agent.create-evaluation', compact('rental'));
    }

    /**
     * Send the evaluation form to the specified user.
     */
    public function sendEvaluationForm(Request $request, string $rentalId)
    {
        $request->validate([
            'recipient' => 'required|in:client,partner,both',
            'message' => 'required|string|max:1000',
            'evaluation_type' => 'required|in:service,dispute,damage',
        ]);

        $rental = Rental::with(['bike', 'renter', 'bike.owner'])->findOrFail($rentalId);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create evaluation form record
            $evaluation = $rental->evaluations()->create([
                'created_by' => Auth::id(),
                'evaluation_type' => $request->evaluation_type,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            // Create notifications based on recipient selection
            if ($request->recipient === 'both' || $request->recipient === 'client') {
                $this->createEvaluationNotification($evaluation, $rental->renter_id, $rental);
            }

            if ($request->recipient === 'both' || $request->recipient === 'partner') {
                $this->createEvaluationNotification($evaluation, $rental->bike->owner_id, $rental);
            }

            DB::commit();

            return redirect()->route('agent.rental.show', $rental->id)
                ->with('success', 'Evaluation form sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while sending the evaluation form: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to create evaluation notifications.
     */
    private function createEvaluationNotification($evaluation, $userId, $rental)
    {
        $notification = new Notification();
        $notification->user_id = $userId;
        $notification->type = 'evaluation_request';
        $notification->notifiable_id = $evaluation->id;
        $notification->notifiable_type = get_class($evaluation);
        $notification->content = 'An agent has requested you to complete an evaluation form for rental #' . $rental->id;
        $notification->is_read = false;

        // Determine correct evaluation form route based on user role
        $user = User::find($userId);
        $notification->link = $user->hasRole('partner')
            ? route('partner.rentals.evaluation', $rental->id)
            : route('rentals.evaluation', $rental->id);

        $notification->save();
    }

    /**
     * Show all comments that need moderation.
     */
    public function moderateComments(Request $request): View
    {
        $user = $request->user();
        $city = $user->profile ? $user->profile->city : null;

        $commentQuery = RentalComment::with(['user', 'rental', 'rental.bike', 'rental.renter'])
            ->where('is_moderated', false)
            ->orderBy('created_at', 'desc');

        // Filter by city if agent has one assigned
        if ($city) {
            $commentQuery->whereHas('rental.bike.owner.profile', function ($query) use ($city) {
                $query->where('city', $city);
            });
        }

        $comments = $commentQuery->paginate(15);

        return view('agent.moderate-comments', [
            'comments' => $comments,
            'city' => $city
        ]);
    }

    /**
     * Approve a comment.
     */
    public function approveComment(string $id)
    {
        $comment = RentalComment::findOrFail($id);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $comment->rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to moderate comments outside your assigned city.');
            }
        }

        $comment->is_moderated = true;
        $comment->moderated_by = Auth::id();
        $comment->moderated_at = now();
        $comment->moderation_status = 'approved';
        $comment->save();

        return redirect()->back()->with('success', 'Comment approved successfully.');
    }

    /**
     * Reject a comment.
     */
    public function rejectComment(Request $request, string $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $comment = RentalComment::findOrFail($id);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $comment->rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to moderate comments outside your assigned city.');
            }
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update comment status
            $comment->is_moderated = true;
            $comment->moderated_by = Auth::id();
            $comment->moderated_at = now();
            $comment->moderation_status = 'rejected';
            $comment->moderation_notes = $request->rejection_reason;
            $comment->save();

            // Notify the comment author
            $notification = new Notification();
            $notification->user_id = $comment->user_id;
            $notification->type = 'comment_rejected';
            $notification->notifiable_id = $comment->id;
            $notification->notifiable_type = get_class($comment);
            $notification->content = 'Your comment on rental #' . $comment->rental_id . ' was rejected by a support agent.';
            $notification->is_read = false;

            // Determine correct route
            $notification->link = $comment->user->hasRole('partner')
                ? route('partner.rentals.show', $comment->rental_id)
                : route('rentals.show', $comment->rental_id);

            $notification->save();

            DB::commit();

            return redirect()->back()->with('success', 'Comment rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while processing this action: ' . $e->getMessage());
        }
    }

    /**
     * Edit a comment (for content moderation).
     */
    public function editComment(string $id): View
    {
        $comment = RentalComment::with(['rental', 'user'])->findOrFail($id);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $comment->rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to edit comments outside your assigned city.');
            }
        }

        return view('agent.edit-comment', compact('comment'));
    }

    /**
     * Update a comment (for content moderation).
     */
    public function updateComment(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'moderation_notes' => 'nullable|string|max:500',
        ]);

        $comment = RentalComment::findOrFail($id);

        $user = Auth::user();
        $city = $user->profile ? $user->profile->city : null;

        // Check if agent is authorized for this rental's location
        if ($city) {
            $rentalCity = $comment->rental->bike->owner->profile->city ?? null;
            if ($rentalCity && $rentalCity !== $city) {
                abort(403, 'You are not authorized to edit comments outside your assigned city.');
            }
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Store original content
            $originalContent = $comment->content;

            // Update comment
            $comment->content = $request->content;
            $comment->is_moderated = true;
            $comment->moderated_by = Auth::id();
            $comment->moderated_at = now();
            $comment->moderation_status = 'edited';
            $comment->moderation_notes = $request->moderation_notes;
            $comment->original_content = $originalContent;
            $comment->save();

            // Notify the comment author
            $notification = new Notification();
            $notification->user_id = $comment->user_id;
            $notification->type = 'comment_edited';
            $notification->notifiable_id = $comment->id;
            $notification->notifiable_type = get_class($comment);
            $notification->content = 'Your comment on rental #' . $comment->rental_id . ' was edited by a support agent.';
            $notification->is_read = false;

            // Determine correct route
            $notification->link = $comment->user->hasRole('partner')
                ? route('partner.rentals.show', $comment->rental_id)
                : route('rentals.show', $comment->rental_id);

            $notification->save();

            DB::commit();

            return redirect()->route('agent.moderate.comments')
                ->with('success', 'Comment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while updating the comment: ' . $e->getMessage());
        }
    }
}
