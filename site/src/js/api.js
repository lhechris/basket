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

export function setPresence(usr,match,val) {
    var baseurl = "/api/";

    axios.post(baseurl, {
        usr:usr,
        match:match,
        value:val
    })
}

export function getSelections() {
    return new Promise( (successClbk,failClbk) => {
        getResource('selections').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setSelection(usr,match,val) {
    var baseurl = "/api/";

    axios.post(baseurl, {
        usr:usr,
        match:match,
        selection:val
    })
}

export function login(login,passwd) {
    var baseurl = "/api/";  
    
    let formdata=new FormData()
    formdata.append("login",login)
    formdata.append("passwd",passwd)
    return new Promise( (successClbk,failClbk) => {
        axios.post(baseurl, formdata).then(response =>{
            successClbk(response.data);

        }).catch(errmsg => {
            failClbk(errmsg)
        }) 
    })  

}

export function islogged() {
    var baseurl = "/api/";

    return new Promise( (successClbk,failClbk) => {
        axios.get(baseurl+'?islogged').then(r => {
            successClbk(r.data)
        }).catch(m => {
            failClbk(m)
        })    
    });
}

export function logout() {
    var baseurl = "/api/";  
    
    let formdata=new FormData()
    formdata.append("logout",true)
    return new Promise( (successClbk,failClbk) => {
        axios.post(baseurl, formdata).then(response =>{
            successClbk(response.data);

        }).catch(errmsg => {
            failClbk(errmsg)
        }) 
    })  

}
