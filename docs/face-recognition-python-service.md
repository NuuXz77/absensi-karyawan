# Face Recognition Python Service

## Overview
Service ini akan memproses foto karyawan dan menghasilkan face embedding untuk face recognition pada sistem absensi.

## Requirements
```bash
pip install flask
pip install opencv-python
pip install dlib
pip install face_recognition
pip install numpy
```

## API Endpoint Structure

### POST /api/process-face
Process foto karyawan dan return face embedding

**Request:**
```json
{
    "image_path": "/path/to/image.jpg",
    "karyawan_id": 1
}
```

**Response Success:**
```json
{
    "success": true,
    "embedding": [0.123, 0.456, ...], // 128-dimensional array
    "model_version": "1.0",
    "karyawan_id": 1
}
```

**Response Error:**
```json
{
    "success": false,
    "error": "No face detected in image"
}
```

## Example Python Flask Service

```python
from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import cv2
import os

app = Flask(__name__)

@app.route('/api/process-face', methods=['POST'])
def process_face():
    try:
        data = request.json
        image_path = data.get('image_path')
        karyawan_id = data.get('karyawan_id')
        
        if not os.path.exists(image_path):
            return jsonify({
                'success': False,
                'error': 'Image file not found'
            }), 404
        
        # Load image
        image = face_recognition.load_image_file(image_path)
        
        # Get face encodings
        face_encodings = face_recognition.face_encodings(image)
        
        if len(face_encodings) == 0:
            return jsonify({
                'success': False,
                'error': 'No face detected in image'
            }), 400
        
        if len(face_encodings) > 1:
            return jsonify({
                'success': False,
                'error': 'Multiple faces detected. Please use image with single face'
            }), 400
        
        # Get the first (and only) face encoding
        embedding = face_encodings[0].tolist()
        
        return jsonify({
            'success': True,
            'embedding': embedding,
            'model_version': '1.0',
            'karyawan_id': karyawan_id
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
```

## Integration dengan Laravel

Di file `App\Livewire\Admin\Karyawan\Modals\Create.php`, update method `processFaceRecognition()`:

```php
private function processFaceRecognition($karyawan, $fotoPath)
{
    try {
        $fullPath = Storage::disk('public')->path($fotoPath);

        // Call Python face recognition service
        $response = Http::timeout(30)->post('http://localhost:5000/api/process-face', [
            'image_path' => $fullPath,
            'karyawan_id' => $karyawan->id,
        ]);

        if ($response->successful() && $response->json('success')) {
            $embedding = $response->json('embedding');
            
            WajahKaryawan::create([
                'karyawan_id' => $karyawan->id,
                'embedding' => json_encode($embedding),
                'model_version' => $response->json('model_version', '1.0'),
            ]);
            
            return true;
        }

        throw new \Exception($response->json('error', 'Unknown error'));

    } catch (\Exception $e) {
        logger()->error('Face recognition processing failed: ' . $e->getMessage());
        return false;
    }
}
```

## Running the Service

1. Save Python code as `face_recognition_service.py`
2. Run the service:
```bash
python face_recognition_service.py
```
3. Service akan berjalan di `http://localhost:5000`

## Testing

Test dengan curl:
```bash
curl -X POST http://localhost:5000/api/process-face \
  -H "Content-Type: application/json" \
  -d '{"image_path": "/path/to/test/image.jpg", "karyawan_id": 1}'
```

## Notes

- Embedding disimpan sebagai JSON array dengan 128 dimensi
- Pastikan foto berkualitas baik dan hanya ada 1 wajah
- Service ini sebaiknya di-deploy terpisah untuk production
- Pertimbangkan menggunakan queue untuk processing async
