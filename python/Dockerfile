FROM python:3.9-slim

WORKDIR /python
COPY chat.py ./
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

CMD ["python", "chat.py"]
