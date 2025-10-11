from abc import ABC, abstractmethod
import time
from datetime import datetime
import torch
import numpy as np

class Command(ABC):
    """Abstract command interface"""
    @abstractmethod
    def execute(self):
        pass

class PredictCommand(Command):
    """Command for executing melanoma prediction"""
    def __init__(self, image_file, model, preprocessing_strategy=None):
        self.image_file = image_file
        self.model = model
        self.preprocessing_strategy = preprocessing_strategy
        self.result = None
    
    def execute(self):
        """Execute the prediction pipeline"""
        from patterns.adapter import MedicalImageAdapter
        from patterns.strategy import PreprocessingContext
        
        try:
            # Adapt image format
            adapter = MedicalImageAdapter()
            image = adapter.adapt(self.image_file)
            
            # Preprocess image
            context = PreprocessingContext(self.preprocessing_strategy)
            processed_image = context.execute_preprocessing(image)
            
            # Convert to tensor
            tensor_image = torch.tensor(processed_image, dtype=torch.float32).permute(2, 0, 1)
            tensor_image = tensor_image.cuda().unsqueeze(0)
            
            # Make prediction
            with torch.no_grad():
                output = self.model(tensor_image)
                prediction = torch.sigmoid(output)[0].item()
            
            self.result = {
                'prediction': prediction,
                'timestamp': datetime.now().isoformat(),
                'status': 'success'
            }
            return self.result
        
        except Exception as e:
            self.result = {
                'error': str(e),
                'timestamp': datetime.now().isoformat(),
                'status': 'error'
            }
            return self.result

class HistoryCommand(Command):
    """Command for retrieving prediction history"""
    def __init__(self, user_id, db_connector):
        self.user_id = user_id
        self.db_connector = db_connector
    
    def execute(self):
        """Retrieve user's prediction history"""
        return self.db_connector.get_user_history(self.user_id)