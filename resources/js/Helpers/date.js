const getDateString = (date) => {
    let string = '';
    const d = new Date(date);
    string = d.getDate() + ' ' + months[d.getMonth()] + ', ' + d.getFullYear();
    return string;
}

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

export default getDateString;