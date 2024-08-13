import json
from apscheduler.schedulers.background import BackgroundScheduler
from flask import Flask
from redis import Redis
from utils.download import download_album
import requests

import logging
_logger = logging.getLogger(__name__)


app = Flask(__name__)
redis = Redis(host='redis', port=6379)


# def process_artist_queue():
#     requests.get('http://nginx/api/queue/artists/run')
#     return

def process_album_queue():
    print('Running Album Queue Process..')
    print('---')
    response = requests.get('http://nginx/api/album/queue')
    data = response.json()
    artist = data.get('artist')
    album = data.get('album')
    queue = data.get('queue')
    if artist and album and queue:
        result = download_album(album, artist)
        requests.post('http://nginx/api/album/queue/update/%s' % queue.get('id'), json=result)
    return

cron = BackgroundScheduler({'apscheduler.job_defaults.max_instances': 1}, daemon=True)
cron.add_job(process_album_queue, 'interval', minutes=1)
cron.start()

if __name__ == "__main__":
    print('Starting App...')
    app.run(host="0.0.0.0", debug=True)
