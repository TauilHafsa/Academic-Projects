from io import BytesIO
from PIL import Image
import cv2
import numpy as np
import pydicom

class MedicalImageAdapter:
    """Adapter pattern for handling different medical image formats"""
    def adapt(self, image_file):
        """Convert different image formats to standardized numpy array"""
        filename = image_file.filename.lower()
        
        if filename.endswith('.dcm'):
            return self._adapt_dicom(image_file)
        elif filename.endswith(('.jpg', '.jpeg', '.png')):
            return self._adapt_standard(image_file)
        else:
            raise ValueError(f"Unsupported image format: {filename.split('.')[-1]}")
    
    def _adapt_dicom(self, image_file):
        """Convert DICOM to RGB numpy array"""
        dicom_data = pydicom.dcmread(BytesIO(image_file.read()))
        image_array = dicom_data.pixel_array
        
        # Handle different color spaces
        if len(image_array.shape) == 2:  # Grayscale
            image_array = np.stack((image_array,) * 3, axis=-1)
        
        return image_array
    
    def _adapt_standard(self, image_file):
        """Convert standard image formats to numpy array"""
        image = Image.open(BytesIO(image_file.read()))
        return np.array(image)