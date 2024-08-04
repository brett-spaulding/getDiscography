import os
from database import Model
from const import *
from .yt_dlp_logger import *
import wget
import yt_dlp

Album = Model('album')


def download_album(album):
    """
    Take a list of albums and process the downloads
    :param album: Dict of album data
    :return:
    """
    artist = album.get('artist')
    artist_path = MEDIA_FOLDER + '/%s' % artist
    if not os.path.exists(artist_path):
        os.mkdir(artist_path)

    print('---')
    # Create album folder
    album_title = album.get('album')
    album_path = artist_path + '/%s' % album_title
    if not os.path.exists(album_path):
        os.mkdir(album_path)

    # Save album cover
    if album.get('cover'):
        try:
            download_file(album.get('cover'), album_path)
        except Exception as e:
            print("Warning: %s" % e)

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

    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        try:
            error_code = ydl.download('https://youtube.com' + album.get('link'))
        except Exception as e:
            print('!!!!!!!!!')
            print(e)
        finally:
            Album.unlink(album['id'])


def download_file(url, output):
    filename = wget.download(url, out=output)
    os.rename(filename, output + '/album.jpg')
    return filename
