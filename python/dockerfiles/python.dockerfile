FROM debian:bullseye-slim
RUN apt update && apt upgrade -y
RUN apt install ffmpeg -y
RUN apt install curl python3-pip -y
ADD . /python
WORKDIR /python

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
