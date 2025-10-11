import torch
import timm
from pathlib import Path

class ViTNet(torch.nn.Module):
    def __init__(self, num_classes=1):
        super(ViTNet, self).__init__()
        self.vit = timm.create_model("vit_base_patch16_224", pretrained=True, num_classes=num_classes)
    
    def forward(self, x):
        return self.vit(x)

class ModelSingleton:
    _instance = None
    _model = None
    
    def __new__(cls):
        if cls._instance is None:
            cls._instance = super(ModelSingleton, cls).__new__(cls)
        return cls._instance
    
    @property
    def model(self):
        if self._model is None:
            self._initialize_model()
        return self._model
    
    def _fix_model_save(self):
        """Fix the model save format to be compatible with weights_only=True"""
        old_model_path = "model_skin_safe.pth"
        new_model_path = "model_skin_safe_fixed.pth"
        
        print("Attempting to fix model save format...")
        
        try:
            # Create a temporary ViTNet class in __main__ namespace to help with unpickling
            import __main__
            __main__.ViTNet = ViTNet
            
            # Also add to torch serialization safe globals
            torch.serialization.add_safe_globals([ViTNet])
            
            print("Loading old model...")
            # Load the old model with weights_only=False
            loaded_object = torch.load(old_model_path, weights_only=False, map_location='cpu')
            
            # Extract state_dict based on what was loaded
            if hasattr(loaded_object, 'state_dict'):
                # It's a model instance
                old_state_dict = loaded_object.state_dict()
                print("Extracted state_dict from model instance")
            elif isinstance(loaded_object, dict):
                # It's already a state_dict
                old_state_dict = loaded_object
                print("Loaded object is already a state_dict")
            else:
                print(f"Unexpected loaded object type: {type(loaded_object)}")
                return False
            
            print("Old model loaded successfully")
            
            # Create a new model instance
            new_model = ViTNet()
            
            # Load the weights (handle potential key mismatches)
            try:
                new_model.load_state_dict(old_state_dict, strict=True)
            except RuntimeError as e:
                print(f"Strict loading failed: {e}")
                print("Trying non-strict loading...")
                new_model.load_state_dict(old_state_dict, strict=False)
            
            print("Weights transferred to new model")
            
            # Save correctly (only the state_dict, not the whole model)
            torch.save(new_model.state_dict(), new_model_path)
            print(f"New model saved: {new_model_path}")
            
            # Test the loading
            test_model = ViTNet()
            test_state_dict = torch.load(new_model_path, weights_only=True, map_location='cpu')
            test_model.load_state_dict(test_state_dict)
            print("Test loading successful!")
            
            return True
            
        except Exception as e:
            print(f"Error during model fix: {e}")
            print(f"Error type: {type(e)}")
            return False
    
    def _initialize_model(self):
        """Initialize the ViT model and load weights"""
        original_model_path = "model_skin_safe.pth"
        fixed_model_path = "model_skin_safe_fixed.pth"
        
        # Check if we have the fixed model
        if not Path(fixed_model_path).exists():
            print("Fixed model not found, attempting to create it...")
            if not self._fix_model_save():
                print("Failed to create fixed model, using original with fallback method")
                model_path = original_model_path
                use_weights_only = False
            else:
                model_path = fixed_model_path
                use_weights_only = True
        else:
            print("Using existing fixed model")
            model_path = fixed_model_path
            use_weights_only = True
        
        try:
            print("Initializing model...")
            device = 'cuda' if torch.cuda.is_available() else 'cpu'
            self._model = ViTNet().to(device)
            
            print(f"Loading model from: {model_path}")
            
            if use_weights_only:
                # Load with security enabled
                state_dict = torch.load(
                    model_path, 
                    map_location=device,
                    weights_only=True
                )
                print("Model loaded with weights_only=True (secure)")
            else:
                # Fallback for original model
                try:
                    # Add ViTNet to __main__ namespace to help with unpickling
                    import __main__
                    __main__.ViTNet = ViTNet
                    
                    # Also try adding to safe globals
                    torch.serialization.add_safe_globals([ViTNet])
                    
                    print("Attempting to load original model with namespace fix...")
                    
                    # First try to load as state_dict
                    loaded_object = torch.load(
                        model_path, 
                        map_location=device,
                        weights_only=False
                    )
                    
                    # Extract state_dict based on what was loaded
                    if hasattr(loaded_object, 'state_dict'):
                        # It's a model instance
                        state_dict = loaded_object.state_dict()
                        print("Extracted state_dict from loaded model instance")
                    elif isinstance(loaded_object, dict):
                        # It's already a state_dict
                        state_dict = loaded_object
                        print("Loaded object is already a state_dict")
                    else:
                        raise Exception(f"Unexpected loaded object type: {type(loaded_object)}")
                    
                    print("Model loaded with weights_only=False (fallback)")
                    
                except Exception as e:
                    print(f"Error loading model: {e}")
                    print(f"Error type: {type(e)}")
                    raise
            
            # Load the state dict into our model
            try:
                self._model.load_state_dict(state_dict, strict=True)
                print("State dict loaded successfully (strict=True)")
            except RuntimeError as e:
                print(f"Strict loading failed: {e}")
                print("Trying non-strict loading...")
                self._model.load_state_dict(state_dict, strict=False)
                print("State dict loaded successfully (strict=False)")
            
            self._model.eval()
            print("Model initialized and set to evaluation mode")
            
        except Exception as e:
            print(f"Error during model initialization: {e}")
            raise Exception(f"Unable to load model: {e}")
    
    def get_model(self):
        """Return the model instance"""
        return self.model