# getDiscography

Flask based utility that will allow a user to download an entire Discography for the provided artist.

## How to use:

Open the root directory of this project in a terminal.

Create a virtual environment with python.
``` 
python -m venv .
```

*For more about virtual environments, look here: https://docs.python.org/3/library/venv.html*

While in the virtual env (venv) install the requirements.txt file with:
``` 
pip install -r ./requirements.txt
```

After all dependencies have been installed successfully, start the service with:
```
python app.py
```

The service will then be accessible at http://localhost for as long as the terminal stays open.  To shutdown the service simply close the terminal or ctl + c

## Known issues
The latest versions of Ubuntu (22.04+ I believe) have swicted over to symlinking Firefox to the snap packages. Which causes an issue with the selenium driver.  I suppose you could probably point to the executable file directly in the snap (not the symlink).  I just use docker with a distro that doesn't have this problem, personally.
