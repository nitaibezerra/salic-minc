const Index = {
    template: `<div>
        <vuetable
            :css="{tableClass: ' bordered'}"
            ref="vuetable"
            api-url="https://vuetable.ratiw.net/api/users"
            :fields="fields"
            >
            </vuetable>
        </div>
    `,
    mounted: function () {
        console.log('teste');
    },
    data: function () {
        return{
            fields: [
                'name', 'email', 'birthdate',
                'address.line1', 'address.line2', 
                {title: 'CEP' , name: 'address.zipcode'},
                {
                  name: '__component:button-counter',   
                  title: 'Actions',
                  titleClass: 'center aligned',
                  dataClass: 'center aligned'
                }
            ]
        }
    }
}
