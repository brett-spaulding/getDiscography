import os
from const import *
from .yt_dlp_logger import *
import wget
import yt_dlp


def download_process_list(artist, processed_albums_data_list):
    """
    Take a list of dictionaries that have the values needed to create a file-structure and save the downloaded files
    :param artist:
    :param processed_albums_data_list:
    :return:
    """
    artist_path = MEDIA_FOLDER + '/%s' % artist
    if not os.path.exists(artist_path):
        os.mkdir(artist_path)

    for item in processed_albums_data_list:
        print('---')
        # Create album folder
        album = item.get('album')
        album_path = artist_path + '/%s' % album
        if not os.path.exists(album_path):
            os.mkdir(album_path)

        # Save album cover
        if item.get('cover'):
            try:
                download_file(item.get('cover'), album_path)
            except Exception as e:
                print("Warning: %s" % e)

        # Download album
        print(item)
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
                error_code = ydl.download('https://youtube.com' + item.get('link'))
            except Exception as e:
                print('!!!!!!!!!')
                print(e)


def download_file(url, output):
    filename = wget.download(url, out=output)
    os.rename(filename, output + '/album.jpg')
    return filename
