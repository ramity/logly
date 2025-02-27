FROM ollama/ollama:latest
WORKDIR /root/.ollama
ADD docker/llm/init.bash /tmp/init.bash
RUN chmod +x /tmp/init.bash
ENTRYPOINT /tmp/init.bash
