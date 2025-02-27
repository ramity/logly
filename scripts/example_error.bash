#!/bin/bash

# Define the URL
URL="http://logly_backend:8000/ingest"

# Define the JSON payload
JSON_PAYLOAD='{ "message": "Uncaught TypeError: Cannot read properties of null (reading property)", "source": "http://192.168.1.4:8000/script.js", "lineno": 4, "colno": 21, "error": "TODO" }'

curl -H "Content-type: application/json"\
     -d '{ "message": "Uncaught TypeError: Cannot read properties of null (reading property)", "source": "http://192.168.1.4:8000/script.js", "lineno": 4, "colno": 21, "error": "TODO", "error_type": "runtime" }'\
     -L\
     -k\
     -X POST\
     http://192.168.1.4:8080/ingest

# Send the POST request and print the response
RESPONSE=$(curl -L -k -X POST "$URL" \
    -H "Content-Type: application/json" \
    -d "$JSON_PAYLOAD")

echo "Response: $RESPONSE"
