import shutil
import os
import yt_dlp
from .yt_dlp_logger import YtDlpLogger, yt_dlp_log_hook

MEDIA_FOLDER = '/var/www/html/storage/app/music'


def download_album(album, artist):
    """
    Take an artist and album dict and use yt-dlp to download the album to the local filestore.
    :param album: Dict of album data
    :return: dict of save data
    """
    response = {
        'artist': {},
        'album': {},
    }
    artist_path = MEDIA_FOLDER + '/%s' % artist.get('name')
    if not os.path.exists(artist_path):
        # Make artist folder and copy the artist image, add
        os.mkdir(artist_path)
        shutil.copy2(artist.get('image'), artist_path + '/artist.jpg')
        response['artist'] = {'url_local': artist_path}

    # Create album folder
    album_title = album.get('name')
    album_path = artist_path + '/%s' % album_title
    if not os.path.exists(album_path):
        os.mkdir(album_path)
        shutil.copy2(album.get('image'), album_path + '/album.jpg')

    # Download album
    ydl_opts = {
        'logger': YtDlpLogger(),
        'progress_hooks': [yt_dlp_log_hook],
        'format': 'mp3/bestaudio/best',
        'outtmpl': album_path + '/%(title)s.%(ext)s',
        # ℹ️ See help(yt_dlp.postprocessor) for a list of available Postprocessors and their arguments
        'postprocessors': [{  # Extract audio using ffmpeg
            'key': 'FFmpegExtractAudio',
            'preferredcodec': 'mp3',
        }]
    }
    download_url = 'https://music.youtube.com/' + album.get('url_remote')

    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        try:
            ydl.download(download_url)
        except Exception as e:
            print('yt-dlp download failed: =========')
            print(e)
    response['album'] = {'url_local': album_path}
    return response
