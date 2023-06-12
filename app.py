from flask import Flask, render_template
from redis import Redis
from utils.processor import process_download


app = Flask(__name__)
redis = Redis(host='redis', port=6379)


@app.route('/')
def index():
    # redis.incr('hits')
    # counter = 'This Compose/Flask demo has been viewed %s time(s).' % redis.get('hits')
    
    return '<h1>Hello World</h1><p>Index Page</p>'


@app.route('/api/v1/get/<path:path>')
def get_artist(path):
    """
    Process for the requested Artist
    :param path: The Artist to get files for
    :return: a status
    """
    proc = process_download(path)
    params = {'artist': path}
    return render_template('base.html', **params)


if __name__ == "__main__":
    print('Starting App...')
    app.run(host="0.0.0.0", debug=True)
