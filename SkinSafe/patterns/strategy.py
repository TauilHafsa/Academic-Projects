import cv2
import numpy as np

class ImagePreprocessingStrategy:
    """Abstract base class for preprocessing strategies"""
    def preprocess(self, image):
        """Process image and return normalized tensor"""
        raise NotImplementedError

class BasicPreprocessing(ImagePreprocessingStrategy):
    """Basic preprocessing strategy (resize + normalize)"""
    def __init__(self, target_size=(224, 224)):
        self.target_size = target_size
    
    def preprocess(self, image):
        """Basic preprocessing pipeline"""
        # Resize
        image = cv2.resize(image, self.target_size)
        # Normalize
        image = image.astype(np.float32)
        return (image / 255.0 - 0.5) / 0.5

class EnhancedContrastPreprocessing(ImagePreprocessingStrategy):
    """Preprocessing with contrast enhancement"""
    def __init__(self, target_size=(224, 224)):
        self.target_size = target_size
    
    def preprocess(self, image):
        """Preprocessing with CLAHE contrast enhancement"""
        # Convert to LAB color space
        lab = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)
        l, a, b = cv2.split(lab)
        
        # Apply CLAHE to L channel
        clahe = cv2.createCLAHE(clipLimit=3.0, tileGridSize=(8, 8))
        cl = clahe.apply(l)
        
        # Merge channels and convert back to BGR
        limg = cv2.merge((cl, a, b))
        image = cv2.cvtColor(limg, cv2.COLOR_LAB2BGR)
        
        # Resize and normalize
        image = cv2.resize(image, self.target_size)
        image = image.astype(np.float32)
        return (image / 255.0 - 0.5) / 0.5

class PreprocessingContext:
    """Context for selecting and executing preprocessing strategies"""
    def __init__(self, strategy: ImagePreprocessingStrategy = None):
        self._strategy = strategy or BasicPreprocessing()
    
    def set_strategy(self, strategy: ImagePreprocessingStrategy):
        self._strategy = strategy
    
    def execute_preprocessing(self, image):
        return self._strategy.preprocess(image)