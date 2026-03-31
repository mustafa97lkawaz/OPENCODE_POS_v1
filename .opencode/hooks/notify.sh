#!/bin/bash

# Notify Hook - runs after every tool execution
EVENT=$1
FILE=$2

echo "✅ [OpenCode Hook] Event: $EVENT | File: $FILE"

# Log to file
echo "$(date '+%Y-%m-%d %H:%M:%S') | $EVENT | $FILE" >> .opencode/hooks/activity.log
