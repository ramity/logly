#!/bin/bash

URL="http://localhost:11434/api/generate"
MODEL="qwen2.5-coder:32b"

curl "$URL" -d "{\"model\": \"$MODEL\", \"keep_alive\": -1}"
