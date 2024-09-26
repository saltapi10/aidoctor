from flask import Flask, request, jsonify
from transformers import pipeline

app = Flask(__name__)
model = pipeline("text-generation", model="EleutherAI/gpt-neo-125M")

@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    prompt = data.get('message', '')
    response = model(prompt, max_length=50)[0]['generated_text']
    return jsonify({'response': response})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
