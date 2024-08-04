FROM debian:bullseye-slim
RUN apt update && apt upgrade -y
RUN apt install firefox-esr  -y
RUN apt install curl python3-pip -y
ADD . /code
WORKDIR /code

# Geckodriver Install for Selenium
# RUN bash geckodriver-install.sh
#RUN json=$(curl -s https://api.github.com/repos/mozilla/geckodriver/releases/latest)
#RUN url=$(echo "$json" | jq -r '.assets[].browser_download_url | select(contains("linux64") and endswith("gz"))')
ARG url="https://github.com/mozilla/geckodriver/releases/download/v0.33.0/geckodriver-v0.33.0-linux64.tar.gz"
RUN curl -s -L "$url" | tar -xz
RUN chmod +x geckodriver
RUN mv geckodriver /usr/local/bin
RUN export PATH=$PATH:/usr/local/bin/geckodriver

RUN pip3 install -r requirements.txt
ENV FLASK_APP=app

# Set user and group
ARG user=app
ARG group=app
ARG uid=1000
ARG gid=1000
RUN groupadd -g ${gid} ${group}
RUN useradd -u ${uid} -g ${group} -s /bin/sh -m ${user}

# Switch to user
USER ${uid}:${gid}

CMD ["python3","-u","app.py"]
