# chat_model.py

import torch
import transformers

from transformers import pipeline
import sys

# Load the model
model = pipeline("text-generation", model="EleutherAI/gpt-neo-125M")

def generate_response(prompt):
    response = model(prompt, max_length=50, num_return_sequences=1)
    return response[0]['generated_text']

if __name__ == "__main__":
    prompt = sys.argv[1]
    print(generate_response(prompt))
