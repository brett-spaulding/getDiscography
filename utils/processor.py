from database import Model
from .scraper import scrape, process_scraped_data
from pysondb import PysonDB


# db = PysonDB('/home/stonesoft/Apps/getDiscography/database/db.json')
Album = Model('album')


def filter_data_list(albums_data_list):
    """
    Ensure there are no duplicate entries or cover-less entries (Intermittent issue when scrape runs)
    :param albums_data_list: A list of dicts that was processed after scrape()
    :return: A clean list of dicts
    """
    processed_albums_data_list = []
    processed_album_names = []
    # Eliminate duplicate entries:
    for item in albums_data_list:
        if item.get('cover') and item.get('album') not in processed_album_names:
            processed_albums_data_list.append(item)
            processed_album_names.append(item.get('album'))

    return processed_albums_data_list


def process_download(artist):
    """
    Main entrypoint for job processing
    :param artist:
    :return:
    """
    artist = artist.title()
    res = {'status': 801, 'message': 'Could not find artist %s' % artist}
    # Initialize a new browser object to go collect the data we need
    try:
        scrape_data = scrape(artist)
        if scrape_data:
            albums_data_list = process_scraped_data(artist, scrape_data)
            processed_albums_data_list = filter_data_list(albums_data_list)
            if len(processed_albums_data_list) == 1:
                Album.create(processed_albums_data_list)
            else:
                Album.create_many(processed_albums_data_list)

            res.update({
                'status': 200,
                'data': processed_albums_data_list,
                'message': 'Artist %s added to the download queue!' % artist
            })
    except Exception as e:
        print(e)

    return res
