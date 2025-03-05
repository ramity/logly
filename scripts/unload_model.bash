#!/bin/bash

URL="http://localhost:11434/api/generate"
MODEL="llama3.3"

curl "$URL" -d "{\"model\": \"$MODEL\", \"keep_alive\": 0}"
