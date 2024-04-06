function getSearch(str) {
  let uic = document.getElementById("suggestions");
  uic.innerHTML = "";
  if (str.length === 0) {
    uic.classList.remove("bg-white");
    return;
  } else {
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        let searchSuggestions = JSON.parse(this.responseText);

        let suggestions = [];

        uic.innerHTML = "";
        searchSuggestions.forEach((delivery) => {
          console.log(delivery);
          suggestions += delivery.name;
          uic.innerHTML +=
            "<li class='list-group-item'><a href='record.php?id=" +
            delivery.id +
            "'>" +
            delivery.name +
            "</a></li>";
        });
        uic.classList.add("bg-white");
      }
    };
    xmlhttp.open("GET", "getSearch.php?q=" + str, true);
    xmlhttp.send();
  }
}
