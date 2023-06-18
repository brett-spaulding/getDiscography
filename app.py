import json
from apscheduler.schedulers.background import BackgroundScheduler
from database import Model
from flask import Flask, render_template
from redis import Redis
from utils.download import download_album
from utils.processor import process_download


app = Flask(__name__)
redis = Redis(host='redis', port=6379)
Album = Model('album')


def process_downloads():
    print('Processing Downloads..')
    pending_downloads = Album.search([('downloaded', '=', False), ('downloading', '=', False)])
    if pending_downloads:
        ready_album = pending_downloads[:1]
        if ready_album:
            album = ready_album[0]
            print('...................')
            print('Downloading Album..')
            print(album)
            Album.write(album['id'], {'downloading': True})
            download_album(album)
    else:
        # Sometimes the records are not being removed
        Album.purge()


cron = BackgroundScheduler({'apscheduler.job_defaults.max_instances': 2}, daemon=True)
cron.add_job(process_downloads, 'interval', minutes=1)
cron.start()


@app.route('/')
def index():
    # redis.incr('hits')
    # counter = 'This Compose/Flask demo has been viewed %s time(s).' % redis.get('hits')
    
    return render_template('base.html')


@app.route('/api/v1/get/artist/<path:path>')
def get_artist(path):
    """
    Process for the requested Artist
    :param path: The Artist to get files for
    :return: a status
    """
    if path:
        res = process_download(path)
    else:
        res = {'status': 501, 'message': 'Could not process download..'}

    return res


@app.route('/api/v1/get/queue')
def get_queue():
    album_ids = Album.search([('downloaded', '=', False)])
    data = {}
    if album_ids:
        data.update({'album_ids': album_ids})
    return render_template('download_queue.html', **data)


if __name__ == "__main__":
    print('Starting App...')
    app.run(host="0.0.0.0", debug=True)
