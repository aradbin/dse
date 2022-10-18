export const getTransactionType = (type) => {
    let string = '';

    if(type===1){
        string = 'Deposit';
    }else if(type===2){
        string = 'Withdraw';
    }else if(type===3){
        string = 'Buy';
    }else if(type===4){
        string = 'Sell';
    }else if(type===5){
        string = 'BO Charge';
    }else if(type===6){
        string = 'IPO Charge';
    }else if(type===7){
        string = 'Cash Dividend';
    }

    return string;
}