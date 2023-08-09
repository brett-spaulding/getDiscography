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