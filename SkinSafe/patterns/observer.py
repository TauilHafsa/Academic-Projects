from flask_socketio import SocketIO

class PredictionObserver:
    """Observer pattern implementation for prediction notifications"""
    def __init__(self):
        self._observers = []
    
    def attach(self, observer):
        if observer not in self._observers:
            self._observers.append(observer)
    
    def detach(self, observer):
        self._observers.remove(observer)
    
    def notify(self, prediction_data):
        """Notify all observers about new prediction"""
        for observer in self._observers:
            observer.update(prediction_data)

class WebObserver:
    """Observer for web notifications using SocketIO"""
    def __init__(self, socketio):
        self.socketio = socketio
    
    def update(self, prediction_data):
        """Send prediction update via WebSocket"""
        self.socketio.emit('prediction_update', {
            'prediction': prediction_data['prediction'],
            'image_url': prediction_data.get('image_url', ''),
            'timestamp': prediction_data.get('timestamp', '')
        })