#!/bin/bash

# Define the URL
# URL="http://logly_backend:8000/ingest"

# Define the JSON payload
# JSON_PAYLOAD='{ "message": "Uncaught TypeError: Cannot read properties of null (reading property)", "source": "http://localhost:8000/script.js", "lineno": 4, "colno": 21, "error": "TODO" }'

curl -H "Content-type: application/json"\
     -d '{ "message": "Uncaught TypeError: Cannot read properties of null (reading property)", "source": "http://localhost:8000/script.js", "lineno": 4, "colno": 21, "error": "TODO", "error_type": "runtime" }'\
     -L\
     -k\
     -X POST\
     http://localhost:8080/ingest
