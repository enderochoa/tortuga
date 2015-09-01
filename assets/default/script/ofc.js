function onrollout()
{
  tmp = findSWF("chart");
  x = tmp.rollout();
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}