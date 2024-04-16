
class AjaxController{

    constructor() {
        this.formData = {}; //Fields used for updating data smoothly without page reload
        this.currentPage = 1;

    }


    async getSearch(search, page = 1) { //Function for livesearch
        if (page < 1) {
            page = 1;
        }
        let uic = document.getElementById("suggestions");
        uic.innerHTML = "";
        if (search.length === 0) {
            uic.classList.remove("bg-white");
        } else {
            const idCheckbox = document.getElementById('idCheckbox');
            const nameCheckbox = document.getElementById('nameCheckbox');
            const addressOneCheckBox = document.getElementById('addressOneCheckbox');
            const addressTwoCheckBox = document.getElementById('addressTwoCheckbox');
            const latCheckBox = document.getElementById('latCheckbox');
            const lngCheckBox = document.getElementById('lngCheckbox');
            const postcodeCheckBox = document.getElementById('postcodeCheckbox');
            const delivererCheckBox = document.getElementById('delivererCheckbox');
            const statusCheckBox = document.getElementById('statusCheckbox');
            let suggestions = [];
            let searchQuery = `ajaxController.php?q=search&search=${search}&page=${page}`
            if (idCheckbox.checked || nameCheckbox.checked || addressOneCheckBox.checked || addressTwoCheckBox.checked || postcodeCheckBox.checked || delivererCheckBox.checked || statusCheckBox.checked || latCheckBox.checked || lngCheckBox.checked) {
                let filters = [];

                if (idCheckbox.checked) {
                    filters.push('id');
                }
                if(nameCheckbox.checked) {
                    filters.push('name');
                }
                if(addressOneCheckBox.checked) {
                    filters.push('addressOne');
                }
                if(addressTwoCheckBox.checked) {
                    filters.push('addressTwo');
                }
                if(latCheckBox.checked) {
                    filters.push('lat');
                }
                if(lngCheckBox.checked) {
                    filters.push('lng');
                }
                if(postcodeCheckBox.checked) {
                    filters.push('postcode');
                }
                if(delivererCheckBox.checked) {
                    filters.push('deliverer');
                }
                if(statusCheckBox.checked) {
                    filters.push('status');
                }

                searchQuery = `ajaxController.php?q=search&search=${search}&page=${page}&filters=${JSON.stringify(filters)}`
            }
            fetch(searchQuery, {method: "GET"}).then(async (res) => {
                return JSON.parse(await res.text());
            }).then((data) => {

                let searchSuggestions = data;
                uic.innerHTML = "";
                searchSuggestions.forEach((delivery) => {
                    suggestions += delivery.name;
                    uic.innerHTML +=
                        "<li class='list-group-item'><a href='record.php?id=" +
                        delivery.id +
                        "'>" +
                        delivery.name + ' ' + delivery.addressOne +
                        "</a></li>";
                });
                let upSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-caret-up-fill" viewBox="0 0 16 16">
  <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
</svg>`;

                let downSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg>`;
                uic.innerHTML += `<li class="list-group-item"><button class="btn button--tertiary" onclick="ajaxController.getSearch('${search}', ${page - 1})">${upSvg}</button><button class="btn button--tertiary mx-2" onclick="ajaxController.getSearch('${search}', ${page + 1})">${downSvg}</button></li>`;
                uic.classList.add("bg-white");
            })

        }
    }
    deleteBtn(id) { //Used to set delete function to the delete button on the modal
        let delBtn = document.getElementById('deleteBtn')
        delBtn.onclick = () => {this.deleteRecord(id)};

    }
    getDeliveries(page = 1) { //Used to fetch deliveries
        let uic = document.getElementById("records");
        uic.innerHTML = '';
        this.currentPage = page;

        fetch(`ajaxController.php?q=deliveries&page=${page}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {
            vectorLayer.removeAllFeatures();
            data.forEach((deliveryPoint) => {
                uic.innerHTML += `<tr onclick="set_position(${deliveryPoint.lat}, ${deliveryPoint.lng})"><td>${deliveryPoint.id}</td><td>${deliveryPoint.name}</td> <td>${deliveryPoint.addressOne}  ${deliveryPoint.addressTwo}<br>Postcode: ${deliveryPoint.postcode}<br> Lat/Lng: ${deliveryPoint.lat}, ${deliveryPoint.lng}</td> <td>${deliveryPoint.username}</td> <td>${deliveryPoint.status}</td><td><img src='Images/${deliveryPoint.photo}' height='75px' width='75px'></td> <td><a id='qrcode${deliveryPoint.id}' href=/record.php?id='${deliveryPoint.id}'></a></td><td ><div class="d-flex flex-column"><button class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="ajaxController.deleteBtn(${deliveryPoint.id})"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></button><button type="button" data-bs-toggle="modal" data-bs-target="#recordModal" onclick="ajaxController.getRecord(${deliveryPoint.id})" class="btn btn-primary mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></button></div></td></tr>`;
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



    getFilteredDeliveries(search) { //Used to get filtered deliveries when filters are applied
        const idCheckbox = document.getElementById('idCheckbox');
        const nameCheckbox = document.getElementById('nameCheckbox');
        const addressOneCheckBox = document.getElementById('addressOneCheckbox');
        const addressTwoCheckBox = document.getElementById('addressTwoCheckbox');
        const postcodeCheckBox = document.getElementById('postcodeCheckbox');
        const delivererCheckBox = document.getElementById('delivererCheckbox');
        const statusCheckBox = document.getElementById('statusCheckbox');

        // let filters = {};
        //
        //
        //
        // let filters = ['name', 'addressOne']
        // return fetch(`ajaxController.php?q=filteredDeliveries&filters=${JSON.stringify(filters)}&search=${search.toString()}`, {method: 'GET'}).then(async (res) => {
        //     return JSON.parse(await res.text());
        // }).then((data) => {
        //     return data;
        // }).catch((err) => {
        //     console.log(err);
        // })
    }

    getRecord(id) { //Gets specific record using the provided id used for updating form
        fetch(`ajaxController.php?q=record&id=${id}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then(async (data) => {

            const recordID = document.getElementById('recordID');
            const recordName = document.getElementById('recordName');
            const recordAddressOne = document.getElementById('recordAddressOne');
            const recordAddressTwo = document.getElementById('recordAddressTwo');
            const recordPostcode = document.getElementById('recordPostcode');
            const recordDeliverer = document.getElementById('recordDeliverer');
            const recordStatus = document.getElementById('recordStatus');
            const recordPhoto = document.getElementById('recordPhoto');



            data.forEach((record) => {
                recordID.innerHTML = record.id;
                if (record.name === null) {
                    recordName.value = 'Not Provided';
                } else {
                    recordName.value = record.name;
                }
                if(record.addressOne === null) {
                    recordAddressOne.value = 'Not Provided';
                }
                else {
                    recordAddressOne.value = record.addressOne;
                }
                if(record.addressTwo === null) {
                    recordAddressTwo.value = 'Not Provided';
                } else {
                    recordAddressTwo.value = record.addressTwo;
                }
                if(record.postcode === null) {
                    recordPostcode.value = 'Not Provided';
                }    else {
                    recordPostcode.value = record.postcode;
                }
                if (record.postcode === null) {
                    recordPostcode.value = 'Not Provided';
                } else {
                    recordPostcode.value = record.postcode;
                }
                if (record.deliverer === null) {
                    recordDeliverer.value = 'Not Provided';
                } else {
                    recordDeliverer.innerHTML += `<option value="${record.delivererID}">${record.username}</option>`;
                }
                if (record.status === null) {
                    recordStatus.value = 'Not Provided';
                } else {
                    console.log(record.statusCode);
                    recordStatus.innerHTML += `<option value="${record.statusCode}">${record.status}</option>`;
                }
                if (record.photo === null) {
                    recordPhoto.filename = null;
                } else {
                    recordPhoto.filename = record.photo;
                }
                this.formData = new recordFormData(record.id, record.name, record.addressOne, record.addressTwo, record.postcode, record.delivererID, record.statusCode, record.photo);
            })

            let statusTypes = await this.getStatusTypes();
            recordStatus.innerHTML = '';
            statusTypes.forEach((status) => {
                recordStatus.innerHTML += `<option value="${status.statusCode}">${status.statusText}</option>`
            })

            let deliverers = await this.getDeliverers();
            recordDeliverer.innerHTML = '';
            deliverers.forEach((deliver) => {
                recordDeliverer.innerHTML += `<option value="${deliver.id}">${deliver.username}</option>`
            })

        }).catch((err) => {
            console.log('Error: ' + err);
        })
    }

    getMarkers() { //Used to get the markers for the live mapping feature
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

    getStatusTypes() { //Used to get StatusTypes
        return fetch('ajaxController.php?q=statusTypes', {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        })
            .then((data) => {
                return data;

        }).catch((err) => {
            console.log(err);
        })

    }

    getDeliverers() { //Used to get the deliverers
        return fetch('ajaxController.php?q=deliverers', {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        })
            .then((data) => {
                return data;

            }).catch((err) => {
                console.log(err);
            })
    }

    getPagination(page) { //Used to provide pagination
        fetch('ajaxController.php?q=total', {method: 'GET'}).then(async (res) => {
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

    editRecord() { //Used for updating records
        const recordName = document.getElementById('recordName');
        const recordAddressOne = document.getElementById('recordAddressOne');
        const recordAddressTwo = document.getElementById('recordAddressTwo');
        const recordPostcode = document.getElementById('recordPostcode');
        const recordDeliverer = document.getElementById('recordDeliverer');
        const recordStatus = document.getElementById('recordStatus');
        const recordPhoto = document.getElementById('recordPhoto');
        const confirmEdit = document.getElementById('confirmEdit');
        let newFormData = new recordFormData();
        if(this.formData.name !== recordName.value) {
            newFormData.name = recordName.value;
        }
        if(this.formData.addressOne !== recordAddressOne.value) {
            newFormData.addressOne = recordAddressOne.value;
        }
        if(this.formData.addressTwo !== recordAddressTwo.value) {
            newFormData.addressTwo = recordAddressTwo.value;
        }
        if(this.formData.postcode !== recordPostcode.value) {
            newFormData.postcode = recordPostcode.value;
        }
        if(this.formData.deliverer !== parseInt(recordDeliverer.value)) {
            newFormData.deliverer = parseInt(recordDeliverer.value);
        }
        if(this.formData.status !== parseInt(recordStatus.value)) {
            newFormData.status = parseInt(recordStatus.value);
        }
        if(this.formData.photo !== recordPhoto.filename) {
            newFormData.photo = recordStatus.filename;
        }
        newFormData.id = this.formData.id;
        let data = JSON.stringify(newFormData);

        if(confirmEdit.checked){
            fetch(`ajaxController.php?q=updateRecord&data=${data}`, {method: 'post'}).then(async (res) => {
                return JSON.parse(await res.text());
            }).then((data) => {
                console.log(data)
                this.getDeliveries(this.currentPage);})
                .catch((err) => {
                console.log(err);
            })
        }
    }

    deleteRecord(id) { //Used to delete records
        fetch(`ajaxController.php?q=delete&id=${id}`, {method: 'post'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {this.getDeliveries(this.currentPage);}

        ).catch((err) => {console.log(err);})
    }
}