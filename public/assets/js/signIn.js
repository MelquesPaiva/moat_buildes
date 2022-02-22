const toastrOptions = {
    closeButton: true,
    progressBar: true,
    timeOut: 7000
}

/**
 * @param {*} value 
 */
function empty(value) {
    return (value === '' || value == null);
}

/**
 * Verificando se elementos com o atributo obrigatório estão preenchidos
 */
 function requiredData(e) {
    let formData = {};

    for (inputForm of e.target.elements) {
        if (['submit', 'button'].indexOf(inputForm.type) === -1) {
            formData[inputForm.name] = inputForm.value;
            if (inputForm.attributes.mandatory != null && (inputForm.value == null || inputForm.value === '' || inputForm.value == 0)) {
                return null;
            }
        }
    }

    return formData;
}

const signInApp = new Vue({
    el: "#mainSignIn",
    methods: {
        login: function(e) {
            this.submitForm(e, "v1/login", function(response) {
                if (response.status === 200 && response.data.data != null) {
                    localStorage.setItem('token', response.data.data.token);
                    window.location.replace(`${host}app`);
                }
            });
        },
        register: function(e) {
            let self = this;
            this.submitForm(e, "v1/register", function(response) {
                if (response.status === 200 && response.data.data != null) {
                    self.clearForm(e.target);
                    toastr.success(response.data.message, 'User', toastrOptions);
                }
            });
        },
        submitForm: function(e, endpoint, callback) {
            let self = this;
            let sendData = requiredData(e);
            if (empty(sendData)) {
                toastr.warning('Enter all mandatory data', 'Sign In', toastrOptions);
                return;
            }

            self.load();

            axios.post(`${host}${endpoint}`, sendData)
                .then(function(response) {
                    callback(response);
                    self.load(false);
                })
                .catch(function(err) {
                    self.load(false);
                    toastr.error(err.response.data.meta.error.message);
                });
        },
        load: function(loading = true) {
            const defaultLoad = $('.default-load');
            if (loading) {
                defaultLoad.css('display', 'flex');
                return;
            }

            defaultLoad.css('display', 'none');
        },
        clearForm: function(form) {
            for (input of form.elements) {
                if (['submit', 'button'].indexOf(input.type) === -1) {
                    input.value = "";
                }
            }
        }
    }
});

