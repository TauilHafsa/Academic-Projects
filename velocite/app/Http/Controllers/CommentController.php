<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\RentalComment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\User;

class CommentController extends Controller
{
    protected $notificationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Display the comments for a rental.
     */
    public function index(string $rentalId)
    {
        $rental = Rental::with(['bike', 'bike.owner', 'comments.user'])->findOrFail($rentalId);

        // Check if user is authorized to view this rental
        if (Auth::id() !== $rental->renter_id && Auth::id() !== $rental->bike->owner_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get all visible comments based on user role
        $isOwner = Auth::id() === $rental->bike->owner_id;

        // Get public comments that are visible to both parties
        $publicComments = $rental->comments()
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get private comments visible to the current user
        $privateComments = $rental->comments()
            ->where('is_private', true)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine comments
        $comments = $publicComments->merge($privateComments)->sortBy('created_at');

        // Determine if both parties have commented or sufficient time has passed
        $clientHasCommented = $rental->comments()->where('user_id', $rental->renter_id)->exists();
        $partnerHasCommented = $rental->comments()->where('user_id', $rental->bike->owner_id)->exists();
        $bothHaveCommented = $clientHasCommented && $partnerHasCommented;

        $oneWeekPassed = Carbon::parse($rental->created_at)->addWeek()->isPast();
        $showAllComments = $bothHaveCommented || $oneWeekPassed;

        return view('comments.index', compact('rental', 'comments', 'showAllComments', 'clientHasCommented', 'partnerHasCommented'));
    }

    /**
     * Show the form for creating a new comment.
     */
    public function create(string $rentalId)
    {
        $rental = Rental::with(['bike', 'bike.owner'])->findOrFail($rentalId);

        // Check if user is authorized to comment on this rental
        if (Auth::id() !== $rental->renter_id && Auth::id() !== $rental->bike->owner_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('comments.create', compact('rental'));
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, string $rentalId)
    {
        $request->validate([
            'content' => 'required|string',
            'is_private' => 'boolean',
        ]);

        $rental = Rental::findOrFail($rentalId);

        // Check if user is authorized to add comments to this rental
        if (!$this->canAddComment(Auth::user(), $rental)) {
            return back()->withErrors(['error' => 'You are not authorized to add comments to this rental.']);
        }

        $comment = new RentalComment();
        $comment->rental_id = $rental->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->is_private = $request->has('is_private') ? true : false;
        $comment->save();

        // Generate notification
        $this->notificationService->notifyNewComment($comment);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'is_private' => 'nullable|boolean',
        ]);

        $comment = RentalComment::findOrFail($id);

        // Check if user is authorized to update this comment
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow updating comments that are less than 24 hours old
        if (Carbon::parse($comment->created_at)->addDay()->isPast()) {
            return back()->with('error', 'Comments can only be edited within 24 hours of creation.');
        }

        $comment->content = $request->content;
        $comment->is_private = $request->has('is_private') && $request->is_private ? true : false;
        $comment->save();

        return redirect()->route(Auth::id() === $comment->rental->renter_id ? 'rentals.show' : 'partner.rentals.show', $comment->rental_id)
            ->with('success', 'Comment updated successfully.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(string $id)
    {
        $comment = RentalComment::findOrFail($id);

        // Check if user is authorized to delete this comment
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deleting comments that are less than 24 hours old
        if (Carbon::parse($comment->created_at)->addDay()->isPast()) {
            return back()->with('error', 'Comments can only be deleted within 24 hours of creation.');
        }

        $rentalId = $comment->rental_id;
        $comment->delete();

        return redirect()->route(Auth::id() === $comment->rental->renter_id ? 'rentals.show' : 'partner.rentals.show', $rentalId)
            ->with('success', 'Comment deleted successfully.');
    }

    /**
     * Determine if a user can add a comment to a rental.
     *
     * @param User $user
     * @param Rental $rental
     * @return bool
     */
    protected function canAddComment($user, Rental $rental)
    {
        // User must be either the renter or the bike owner
        return $user->id === $rental->renter_id || $user->id === $rental->bike->owner_id;
    }
}