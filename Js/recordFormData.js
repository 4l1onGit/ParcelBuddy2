class recordFormData {
    constructor(id,name, addressOne, addressTwo, postcode, deliverer, status, photo) {
        this._id = id;
        this._name = name;
        this._addressOne = addressOne;
        this._addressTwo = addressTwo;
        this._postcode = postcode;
        this._deliverer = deliverer;
        this._status = status;
        this._photo = photo;
    }

    get id() {
        return this._id;
    }

    set id(value) {
        this._id = value;
    }
    get name() {
        return this._name;
    }

    set name(value) {
        this._name = value;
    }

    get addressOne() {
        return this._addressOne;
    }

    set addressOne(value) {
        this._addressOne = value;
    }

    get addressTwo() {
        return this._addressTwo;
    }

    set addressTwo(value) {
        this._addressTwo = value;
    }

    get postcode() {
        return this._postcode;
    }

    set postcode(value) {
        this._postcode = value;
    }

    get deliverer() {
        return this._deliverer;
    }

    set deliverer(value) {
        this._deliverer = value;
    }

    get status() {
        return this._status;
    }

    set status(value) {
        this._status = value;
    }

    get photo() {
        return this._photo;
    }

    set photo(value) {
        this._photo = value;
    }



}