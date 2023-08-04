import axios from 'axios';

function getResource(url) {

    var baseurl = "/api/";

    return new Promise((successClbk,failClbk) => {

        axios.get(baseurl+'?'+url).then(response =>{
        var r=response.data;        
        successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })

    });

}

export function getMatches() {
    return new Promise( (successClbk,failClbk) => {
        getResource('matchs').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function getUsers() {
    return new Promise( (successClbk,failClbk) => {
        getResource('users').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function getPresences() {
    return new Promise( (successClbk,failClbk) => {
        getResource('presences').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setPresences(usr,match,val) {
    var baseurl = "/api/";

    axios.post(baseurl, {
        usr:usr,
        match:match,
        value:val
    })
}