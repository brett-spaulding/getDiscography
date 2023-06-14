from database import Model
from flask import Flask, render_template
from redis import Redis
from utils.processor import process_download


app = Flask(__name__)
redis = Redis(host='redis', port=6379)
Album = Model('album')


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
    data = {'album_ids': album_ids}
    print('======================')
    print(data)
    return render_template('download_queue.html', **data)


if __name__ == "__main__":
    print('Starting App...')
    app.run(host="0.0.0.0", debug=True)
