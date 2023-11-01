export const swalSuccess = message => {
    return Swal.fire({
        icon: "success",
        title: message,
        // text: message,
        showConfirmButton: false,
        timer: 2000
    })
};

export const swalError = message => {
    return Swal.fire({
        icon: "error",
        title: "Greška!",
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'U redu',
        confirmButtonColor: '#138496'
    });
};

export const getFileNameFromPath = path => {
    const pathArray = path.split("/");
    console.log(pathArray);
    return pathArray[pathArray.length - 1];
};

export const checkIfEveryNotEmpty = (values) => {
    let args = [...arguments];
    console.log(args);
};

export const checkIfNotEmpty = value => value !== null && value !== undefined && value !== "";
export const stripHtmlTags = string => string.replace(/(<([^>]+)>)/gi,"");

export const defaultOptions = {
    count: "Prikazano {from} do {to} od {count} unosa|{count} unosa|Jedan unos",
    first: 'Prva',
    last: 'Poslednja',
    filter: " ",
    filterPlaceholder: "Pretraga",
    limit: " ",
    page: "Stranica:",
    noResults: "Nema podataka",
    loading: 'Loading...',
    columns: 'Columns'
};

export const sortIcon = {
    is: 'fa-sort',
    base: 'fa',
    up: 'fa-sort-up',
    down: 'fa-sort-down',
};

import Vue from 'vue';
export const EventBus = new Vue();
