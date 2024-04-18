
class AjaxController{

    constructor(user = 'deliverer', id) {
        this.formData = {}; //Fields used for updating data smoothly without page reload
        this.currentPage = 1;
        this.user = user
        this.recordID = document.getElementById('recordID');
        this.recordName = document.getElementById('recordName');
        this.recordAddressOne = document.getElementById('recordAddressOne');
        this.recordAddressTwo = document.getElementById('recordAddressTwo');
        this.recordPostcode = document.getElementById('recordPostcode');
        this.recordLat = document.getElementById('recordLat');
        this.recordLng = document.getElementById('recordLng');
        this.recordDeliverer = document.getElementById('recordDeliverer');
        this.recordStatus = document.getElementById('recordStatus');
        this.recordPhoto = document.getElementById('recordPhoto');
        this.recordButton = document.getElementById('recordButton');
    }

    async getSearch(search, page = 1, token) { //Function for livesearch
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
            let delivererFilter = document.getElementById('delivererFilter');
            let delivererId = null;
            let searchQuery = `ajaxController.php?q=search&search=${search}&page=${page}&token=${token}`
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
                    delivererId = delivererFilter.value;
                }
                if(statusCheckBox.checked) {
                    filters.push('status');
                }

                searchQuery = `ajaxController.php?q=search&search=${search}&page=${page}&filters=${JSON.stringify(filters)}&token=${token}`
                if(delivererId !== null) {
                    searchQuery += `&id=${delivererId}`;
                }

            }
            fetch(searchQuery, {method: "GET"}).then(async (res) => {
                return JSON.parse(await res.text());
            }).then((data) => {
                console.log(data);
                let searchSuggestions = data;
                uic.innerHTML = "";

                searchSuggestions.forEach((delivery) => {
                    suggestions += delivery.name;
                    uic.innerHTML +=
                        `<li class='list-group-item'>
                        <div class="d-flex justify-content-between z-3"><div>ID: ${delivery.id} Name: ${delivery.name} ${delivery.addressOne}<div><button type="button" data-bs-toggle="modal" data-bs-target="#recordModal" onclick="ajaxController.getRecord(${delivery.id}, '${token}')" class="btn btn-primary mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></button> </div>
                       </li>`;
                });
                let upSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-caret-up-fill" viewBox="0 0 16 16">
  <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
</svg>`;

                let downSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg>`;
                uic.innerHTML += `<li class="list-group-item"><button class="btn button--tertiary" onclick="ajaxController.getSearch('${search}', ${page - 1}, '${token}')">${upSvg}</button><button class="btn button--tertiary mx-2" onclick="ajaxController.getSearch('${search}', ${page + 1}, '${token}')">${downSvg}</button></li>`;
                uic.classList.add("bg-white");
            })

        }
    }
    deleteBtn(id) { //Used to set delete function to the delete button on the modal
        let delBtn = document.getElementById('deleteBtn')
        delBtn.onclick = () => {this.deleteRecord(id)};

    }
    getDeliveries(page = 1, token, view = 'desktop') { //Used to fetch deliveries

        this.currentPage = page;
        this.currentView = view;
        let mobileContainer = document.getElementById('mobile');
        fetch(`ajaxController.php?q=deliveries&page=${page}&token=${token}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {
            vectorLayer.removeAllFeatures();
            if(window.getComputedStyle(mobileContainer).display === "none") {
                console.log('Refresh');
                let uic = document.getElementById("recordsDesktop");
                uic.innerHTML = '';
                data.forEach((deliveryPoint) => {
                    uic.innerHTML += `<tr onclick="set_position(${deliveryPoint.lat}, ${deliveryPoint.lng})"><td>${deliveryPoint.id}</td><td>${deliveryPoint.name}</td> <td>${deliveryPoint.addressOne}  ${deliveryPoint.addressTwo}<br>Postcode: ${deliveryPoint.postcode}<br> Lat/Lng: ${deliveryPoint.lat}, ${deliveryPoint.lng}</td> <td>${deliveryPoint.username}</td> <td>${deliveryPoint.status}</td><td><img src='Images/${deliveryPoint.photo}' height='75px' width='75px'></td> <td><a id='qrcode${deliveryPoint.id}' href=/record.php?id='${deliveryPoint.id}'></a></td><td ><div class="d-flex flex-column"><button class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="ajaxController.deleteBtn(${deliveryPoint.id})"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></button><button type="button" data-bs-toggle="modal" data-bs-target="#recordModal" onclick="ajaxController.getRecord(${deliveryPoint.id}, '${token}')" class="btn btn-primary mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></button></div></td></tr>`;
                    createLocation(deliveryPoint.lat, deliveryPoint.lng, `Name: ${deliveryPoint.name}`  , `${deliveryPoint.photo}`);
                })
            } else {
                let uic = document.getElementById("recordsMobile");
                uic.innerHTML = '';
                data.forEach((deliveryPoint) => {
                    uic.innerHTML += `<tr onclick="set_position(${deliveryPoint.lat}, ${deliveryPoint.lng})"><td><div><ul class="list-group"><li>Name: ${deliveryPoint.name}</li><li>Address: ${deliveryPoint.addressOne} ${deliveryPoint.addressTwo} ${deliveryPoint.addressTwo}</li><li>Postcode: ${deliveryPoint.postcode}</li><li>Lat/Lng: ${deliveryPoint.lat}/${deliveryPoint.lng}</li> <li class="list-item" id='qrcode${deliveryPoint.id}'></li></div></td><td><div class="d-flex flex-column"><button class="btn btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="ajaxController.deleteBtn(${deliveryPoint.id})"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></button><button type="button" data-bs-toggle="modal" data-bs-target="#recordModal" onclick="ajaxController.getRecord(${deliveryPoint.id}, '${token}')" class="btn btn-primary mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></button></div></td></td><td><img src='Images/${deliveryPoint.photo}' height='75px' width='75px'></td></tr>`
                    createLocation(deliveryPoint.lat, deliveryPoint.lng, `Name: ${deliveryPoint.name}`  , `${deliveryPoint.photo}`);
                })
            }
            data.forEach((deliveryPoint) => {
                this.createQRcode(deliveryPoint);
            })
            this.getPagination(page, token);
        }).catch((err) => {
            console.log('Error:' + err);
        });
    }

    createQRcode(deliveryPoint) {
        let mobileContainer = document.getElementById('mobile');
        try {
            let qrcodeElement;
            if(window.getComputedStyle(mobileContainer).display === "none") {
                qrcodeElement = document.getElementById(`qrcode${deliveryPoint.id}`);
            } else {
                qrcodeElement = document.getElementById(`qrcode${deliveryPoint.id}`);
            }

            new QRCode(qrcodeElement, {
                text: window.location.hostname + "/record.php?id=" + deliveryPoint.id,
                width: 75,
                height: 75
            });

        } catch(e) {
            console.log(e);
        }
    }

    getRecord(id, token) { //Gets specific record using the provided id used for updating form
        fetch(`ajaxController.php?q=record&id=${id}&token=${token}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then(async (data) => {
            data.forEach((record) => {

                this.recordID.innerHTML = record.id;

                if (record.name === null) {
                    this.recordName.value = 'Not Provided';
                } else {

                    this.recordName.value = record.name;
                }
                if(record.addressOne === null) {
                    this.recordAddressOne.value = 'Not Provided';
                }
                else {
                    this.recordAddressOne.value = record.addressOne;
                }
                if(record.addressTwo === null) {
                    this.recordAddressTwo.value = 'Not Provided';
                } else {
                    this.recordAddressTwo.value = record.addressTwo;
                }
                if(record.postcode === null) {
                    this.recordPostcode.value = 'Not Provided';
                }    else {
                    this.recordPostcode.value = record.postcode;
                }
                if(record.lat === null ) {
                    this.recordLat.value = 'Not Provided';
                } else {
                    this.recordLat.value = record.lat;
                }
                if(record.lng === null) {
                    this.recordLng.value = 'Not Provided';
                } else {
                    this.recordLng.value = record.lng;
                }
                if (record.postcode === null) {
                    this.recordPostcode.innerHTML = '';
                } else {
                    this.recordPostcode.value = record.postcode;
                }
                if (record.delivererID === null) {
                    this.recordDeliverer.innerHTML = `<option>No Deliverer</option>`;
                } else {
                    this.recordDeliverer.innerHTML = `<option value="${record.delivererID}">${record.username}</option>`;

                }
                if (record.status === null) {
                    this.recordStatus.value = 'Not Provided';
                } else {
                    this.recordStatus.innerHTML = record.statusCode
                }
                if (record.photo === null) {
                    this.recordPhoto.filename = '';
                } else {
                    this.recordPhoto.filename = record.photo;
                }

                if(this.user === 'manager') {
                    this.setDeliverers(token).then(r => {});
                }
                this.setStatusTypes(token).then(r => {});
                this.formData = new recordFormData(record.id, record.name, record.addressOne, record.addressTwo, record.postcode, record.delivererID, record.statusCode, record.lat, record.lng, record.photo);
            })





            this.recordButton.onclick =  () => {this.editRecord(token)};
            this.recordButton.innerHTML = 'Edit'
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

    getStatusTypes(token) { //Used to get StatusTypes
        return fetch(`ajaxController.php?q=statusTypes&token=${token}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        })
            .then((data) => {
                return data;

            }).catch((err) => {
                console.log(err);
            })

    }

    getDeliverers(token) { //Used to get the deliverers
        return fetch(`ajaxController.php?q=deliverers&token=${token}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        })
            .then((data) => {
                return data;

            }).catch((err) => {
                console.log(err);
            })
    }

    getPagination(page, token) { //Used to provide pagination
        fetch(`ajaxController.php?q=total&token=${token}`, {method: 'GET'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {

            let totalItems = data;
            const recordsPerPage = 10;
            let pageCount = Math.ceil(totalItems / recordsPerPage);
            let pageNavElement = document.getElementById('page-nav');
            pageNavElement.innerHTML = '';

            if (pageCount > 1) {
                if (page > 1) {
                    pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page-1}, '${token}')" class="page-link">Prev</a></li>`;
                }
                if (pageCount < 5) {
                    for (let x = 1; x <= pageCount; x++) {
                        pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${x}, '${token}')" class="page-link" id="pageNav${x}">${x}</a></li>`;
                    }
                }
                else {
                    for (let x = 1; x <= 5; x++) {
                        pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${x}, '${token}')" class="page-link" id="pageNav${x}">${x}</a></li>`;
                    }
                    if(page > 5 && page < pageCount) {
                        pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page}, '${token}')" class="page-link" id="pageNav${page}">${page}</a></li>`;

                    }
                    if(page > 4 && page < pageCount) {
                        pageNavElement.innerHTML += `<li class="page-item"><a onclick="ajaxController.getDeliveries(${page+1}, '${token}')" class="page-link" id="pageNav${page+1}">${page+1}</a></li>`;
                    }


                    pageNavElement.innerHTML +=  `<li class="page-item"><a onclick="ajaxController.getDeliveries(${pageCount}, '${token}')" class="page-link" id="pageNav${pageCount}">${pageCount}</a></li>`;
                }
                if (page < pageCount) {
                    pageNavElement.innerHTML += `<li class="page-item"><a class="page-link" onclick="ajaxController.getDeliveries(${page+1}, '${token}')"'">Next</a></li>`;
                }
            }
            if(pageCount > 1) {
                let currentPageElement = document.getElementById(`pageNav${page}`)
                currentPageElement.classList.add("pagination--active");
            }

        }).catch((err) => {
            console.log('Error:' + err);
        });


    }

    editRecord(token) { //Used for updating records

        const confirmEdit = document.getElementById('confirmRecord');

        let newFormData = new recordFormData();
        newFormData.id = this.formData.id;
        if(this.formData.name !== this.recordName.value) {
            newFormData.name = this.recordName.value;
        }
        if(this.formData.addressOne !== this.recordAddressOne.value) {
            newFormData.addressOne = this.recordAddressOne.value;
        }
        if(this.formData.addressTwo !== this.recordAddressTwo.value) {
            newFormData.addressTwo = this.recordAddressTwo.value;
        }
        if(this.formData.postcode !== this.recordPostcode.value) {
            newFormData.postcode = this.recordPostcode.value;
        }

            newFormData.deliverer = parseInt(this.recordDeliverer.value);

        if(this.formData.status !== parseInt(this.recordStatus.value)) {
            newFormData.status = parseInt(this.recordStatus.value);
        }
        if(this.formData.lat !== parseFloat(this.recordLat.value)) {
            newFormData.lat = parseFloat(this.recordLat.value);
        }
        if(this.formData.lng !== parseFloat(this.recordLng.value)) {
            newFormData.lng = parseFloat(this.recordLng.value);
        }

        newFormData.id = this.formData.id;
        let data = JSON.stringify(newFormData);

        if(confirmEdit.checked){
            fetch(`ajaxController.php?q=updateRecord&data=${data}&token=${token}`, {method: 'post'}).then(async (res) => {
                return JSON.parse(await res.text());
            }).then((data) => {
                console.log(data, this.currentView);
                this.getDeliveries(this.currentPage, token, this.currentView);})
                .catch((err) => {
                    console.log(err);
                })
        }
    }

    async setDeliverers(token, element = this.recordDeliverer) {
        let deliverers = await this.getDeliverers(token);
        console.log(deliverers);
        element.innerHTML = '';
        deliverers.forEach((deliver) => {

            element.innerHTML += `<option value="${deliver.id}">${deliver.username}</option>`
        })
    }

    async setStatusTypes(token) {
        let statusTypes = await this.getStatusTypes(token);
        this.recordStatus.innerHTML = '';
        statusTypes.forEach((status) => {
            this.recordStatus.innerHTML += `<option value="${status.statusCode}">${status.statusText}</option>`
        })
    }

    setUpCreateForm(token) {
        this.recordID.innerHTML = 'Create Record';
        this.setStatusTypes(token).then(r => {});
        this.setDeliverers(token).then(r => {});
        this.recordID.value = '';
        this.recordName.value = '';
        this.recordAddressOne.value = '';
        this.recordAddressTwo.value = '';
        this.recordPostcode.value = '';
        this.recordLat.value = '';
        this.recordLng.value = '';
        this.recordButton.onclick =  () => {ajaxController.createRecord(token)};
        this.recordButton.innerHTML = 'Create';

    }

    async createRecord(token) {
        console.log('hi');
        const confirmCreate = document.getElementById('confirmCreate');

        let newFormData = new recordFormData();

        if (this.recordName.value !== '') {
            newFormData.name = this.recordName.value;
        }
        if (this.recordAddressOne.value !== '') {
            newFormData.addressOne = this.recordAddressOne.value;
        }
        if (this.recordAddressTwo.value !== '') {
            newFormData.addressTwo = this.recordAddressTwo.value;
        }
        if (this.recordPostcode.value !== '') {
            newFormData.postcode = this.recordPostcode.value;
        }
        if (this.recordLat.value !== null) {
            newFormData.lat = parseFloat(this.recordLat.value);
        }
        if (this.recordLng.value !== null) {
            newFormData.lng = parseFloat(this.recordLng.value);
        }
        if (parseInt(this.recordDeliverer.value) !== null) {
            newFormData.deliverer = parseInt(this.recordDeliverer.value);
        }
        if (parseInt(this.recordStatus.value) !== null) {
            newFormData.status = parseInt(this.recordStatus.value);
        }


        let data = JSON.stringify(newFormData);


        if (confirmCreate.checked) {
            fetch(`ajaxController.php?q=createRecord&data=${data}&token=${token}`, {method: 'post'}).then(async (res) => {
                return JSON.parse(await res.text());
            }).then((data) => {
                console.log(data)
                this.getDeliveries(this.currentPage, token, this.currentView);
            })
                .catch((err) => {
                    console.log(err);
                })
        }
    }

    deleteRecord(id, token) { //Used to delete records
        fetch(`ajaxController.php?q=delete&id=${id}&token=${token}`, {method: 'post'}).then(async (res) => {
            return JSON.parse(await res.text());
        }).then((data) => {this.getDeliveries(this.currentPage), token, this.currentView}

        ).catch((err) => {console.log(err);})
    }
}