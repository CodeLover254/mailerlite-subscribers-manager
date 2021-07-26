export default class DataProcessor{
    /**
     * @param {object[]} data
     */
    static extractCountryName(data){
        const filtered = data.filter((obj)=>obj.key==='country');
        return filtered.length > 0?filtered[0].value:'Not Available';
    }

    /**
     * @param {string} data
     */
    static extractDate(data){
        const dt = new Date(data);
        return `${dt.getDay()}/${dt.getMonth()}/${dt.getFullYear()}`;
    }

    static addLeadingZeros(num){
        return num < 10? '0'+num.toString():num;
    }

    /**
     * @param {string} data
     */
    static extractTime(data){
        const dt = new Date(data);

        const hours = this.addLeadingZeros(dt.getHours());
        const minutes = this.addLeadingZeros(dt.getMinutes());
        const seconds = this.addLeadingZeros(dt.getSeconds());

        return  `${hours}:${minutes}:${seconds}`;
    }
}
