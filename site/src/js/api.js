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

export function getMatch(id) {
    return new Promise( (successClbk,failClbk) => {
        getResource('match='+id).then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function getMatchsAvecOpp() {
    return new Promise( (successClbk,failClbk) => {
        getResource('matchsavecopp').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });

}


export function getMatchsAvecSel() {
    return new Promise( (successClbk,failClbk) => {
        getResource('matchsavecsel').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setMatch(onematch) {
    var baseurl = "/api/";    
    return new Promise((successClbk,failClbk) => {
        var data={"type":"match", tab:onematch}
        axios.post(baseurl, data).then(response =>{
        var r=response.data;        
        successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })

    });
}


export function setOpposition(matchid,userid,val) {
    var baseurl = "/api/";

    return new Promise((successClbk,failClbk) => {
        axios.post(baseurl, {
            usr:userid,
            match:matchid,
            opposition:val
        }).then(response =>{
            var r=response.data;        
            successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })

    });
}

export function getEntrainements() {
    return new Promise( (successClbk,failClbk) => {
        getResource('entrainements').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}


export function getUsers() {
    return new Promise( (successClbk,failClbk) => {
        getResource('users').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setUsers(users) {
    var baseurl = "/api/";
    
    return new Promise((successClbk,failClbk) => {
        var data={"type":"users", tab:users}
        axios.post(baseurl, data).then(response =>{
        var r=response.data;        
        successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })

    });

}


export function getPresences() {
    return new Promise( (successClbk,failClbk) => {
        getResource('presences').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setPresence(usr,match,val) {
    var baseurl = "/api/";

    return new Promise((successClbk,failClbk) => {

            axios.post(baseurl, {
            usr:usr,
            entrainement:match,
            pres:val
        }).then(response =>{
            var r=response.data;        
            successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })
    });
}
export function getDisponibilites() {
    return new Promise( (successClbk,failClbk) => {
        getResource('disponibilites').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setDisponibilite(usr,jour,val) {
    var baseurl = "/api/";

    return new Promise((successClbk,failClbk) => {

        axios.post(baseurl, {
            usr:usr,
            jour:jour,
            value:val
        }).then(response =>{
            var r=response.data;        
            successClbk(r);

        }).catch(errmsg => {
            failClbk(errmsg);
        })

    });



}
export function getSelections() {
    return new Promise( (successClbk,failClbk) => {
        getResource('selections').then(r => {successClbk(r)}).catch(m => {failClbk(m)})    
    });
}

export function setSelection(usr,match,val) {
    var baseurl = "/api/";

    return new Promise((successClbk,failClbk) => {

    axios.post(baseurl, {
        usr:usr,
        match:match,
        selection:val
    }).then(response =>{
        var r=response.data;        
        successClbk(r);

    }).catch(errmsg => {
        failClbk(errmsg);
    })

});
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

import moment from 'moment'
import 'moment/dist/locale/fr';

export function displaydate(d) {
    return moment(d).local('fr').format("dddd Do MMMM")
}