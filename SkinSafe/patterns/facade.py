import torch
from datetime import datetime

class MelanomaDetectionFacade:
    """Facade pattern to simplify the melanoma detection pipeline"""
    def __init__(self, socketio=None):
        try:
            from patterns.singleton import ModelSingleton
            from patterns.observer import PredictionObserver, WebObserver
            
            print("Initializing MelanomaDetectionFacade...")
            
            # Initialize model singleton (this will trigger model loading)
            model_singleton = ModelSingleton()
            self.model = model_singleton.model
            
            print("Model loaded successfully in facade")
            
            # Initialize observer pattern
            self.observer = PredictionObserver()
            
            if socketio:
                self.observer.attach(WebObserver(socketio))
                print("WebSocket observer attached")
            
            print("MelanomaDetectionFacade initialized successfully")
            
        except Exception as e:
            print(f"Error initializing MelanomaDetectionFacade: {e}")
            raise Exception(f"Failed to initialize melanoma detection system: {e}")
    
    def process_image(self, image_file):
        """Simplified interface for processing an image"""
        try:
            from patterns.command import PredictCommand
            
            # Validate that model is available
            if self.model is None:
                raise Exception("Model not available")
            
            # Create and execute command
            command = PredictCommand(image_file, self.model)
            result = command.execute()
            
            # Notify observers if successful
            if result['status'] == 'success':
                self.observer.notify({
                    'prediction': result['prediction'],
                    'timestamp': result['timestamp']
                })
            
            return result
            
        except Exception as e:
            print(f"Error processing image: {e}")
            return {
                'status': 'error',
                'message': f'Failed to process image: {str(e)}',
                'timestamp': datetime.now().isoformat()
            }
    
    def is_model_ready(self):
        """Check if the model is ready for inference"""
        return self.model is not None and hasattr(self.model, 'eval')