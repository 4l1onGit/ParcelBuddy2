
class AjaxController {

    constructor() {
    }
    getSearch(search) {
        let uic = document.getElementById("suggestions");
        uic.innerHTML = "";
        if (search.length === 0) {
            uic.classList.remove("bg-white");
        } else {
           fetch(`getSearch.php?q=${search}`, {method: "GET"}).then(async (res) => {
               return JSON.parse(await res.text());
            }).then((data) => {
               let searchSuggestions = data;

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
           })
        }
    }

    getDeliveries(page = 1) {
        let uic = document.getElementById("records");
        uic.innerHTML = '';

        fetch('getDeliveries.php?q=deliveries&page=' + page, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {
            vectorLayer.removeAllFeatures();
            data.forEach((deliveryPoint) => {
                uic.innerHTML += `<tr onclick="set_position(${deliveryPoint.lat}, ${deliveryPoint.lng})"><td>${deliveryPoint.id}</td><td>${deliveryPoint.name}</td> <td>${deliveryPoint.addressOne}  ${deliveryPoint.addressTwo}<br>Postcode: ${deliveryPoint.postcode}<br> Lat/Lng: ${deliveryPoint.lat}, ${deliveryPoint.lng}</td> <td>${deliveryPoint.username}</td> <td>${deliveryPoint.status}</td><td><img src='Images/${deliveryPoint.photo}' height='75px' width='75px'></td> <td><a id='qrcode${deliveryPoint.id}' href=/record.php?id='${deliveryPoint.id}'></a></td></tr>`;
                createLocation(deliveryPoint.lat, deliveryPoint.lng, `Name: ${deliveryPoint.name}`  , `${deliveryPoint.photo}`);
            })
            data.forEach((deliveryPoint) => {
                try {
                    new QRCode(document.getElementById("qrcode" + deliveryPoint.id), {
                        text: window.location.hostname + "/record.php?id=" + deliveryPoint.id,
                        width: 75,
                        height: 75
                    });
                } catch(e) {
                    console.log(e);
                }
            })
            this.getPagination(page);
        }).catch((err) => {
            console.log('Error:' + err);
        });
    }

    getFilteredDeliveries(filters) {

    }

    getRecord(id) {
        fetch(`getDeliveries.php?q=record&id=${id}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {

        }).catch((err) => {
            console.log('Error: ' + err);
        })
    }

    getMarkers() {
        fetch("getDeliveries.php?q=deliveries", {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {
            data.forEach((markersData) => {
                createLocation(markersData.lat, markersData.lng, `Name: ${markersData.name}`  , `${markersData.photo}`);
            })
        }).catch((err) => {
            console.log('Error: ' + err);
        })
    }

    getPagination(page) {
        fetch('getDeliveries.php?q=total', {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {

            let totalItems = data;
            const recordsPerPage = 10;
            let pageCount = Math.ceil(totalItems / recordsPerPage);
            let pageNavElement = document.getElementById('page-nav');
            pageNavElement.innerHTML = '';

                if (pageCount > 1) {
                       if (page > 1) {
                           pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page-1})" class="page-link">Prev</a></li>`;
                       }
                       if (pageCount < 5) {
                           for (let x = 1; x <= pageCount; x++) {
                               pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${x})" class="page-link" id="pageNav${x}">${x}</a></li>`;
                           }
                       }
                       else {
                           for (let x = 1; x <= 5; x++) {
                               pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${x})" class="page-link" id="pageNav${x}">${x}</a></li>`;
                           }
                           if(page > 5 && page < pageCount) {
                               pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page})" class="page-link" id="pageNav${page}">${page}</a></li>`;

                           }
                           if(page > 4 && page < pageCount) {
                               pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page+1})" class="page-link" id="pageNav${page+1}">${page+1}</a></li>`;
                           }


                           pageNavElement.innerHTML +=  `<li class="page-item"><a onclick="ajaxController.getDeliveries(${pageCount})" class="page-link" id="pageNav${pageCount}">${pageCount}</a></li>`;
                       }
                       if (page < pageCount) {
                        pageNavElement.innerHTML += `<li class="page-item"><a class="page-link" onclick="ajaxController.getDeliveries(${page+1})"'">Next</a></li>`;
                       }
                   }
            let currentPageElement = document.getElementById(`pageNav${page}`)
            currentPageElement.classList.add("pagination--active");
        }).catch((err) => {
            console.log('Error:' + err);
        });
    }
}