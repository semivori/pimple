// Try edit message


const getData = async () => {
    const res = await fetch('https://sv-fas.herokuapp.com/pimple.php');
    return await res.json();
};

const copyToClipboard = {
    click: function (e) {
        const input = document.createElement('input');
        input.value = e.currentTarget.dataset.copy;
        console.log(e.target.dataset.copy);
        document.body.append(input);
        input.select();
        document.execCommand("copy");
        input.remove();

    }
};

const addDataToTable = (data) => {
    const table = document.getElementById('js__pimple__matches-table');
    const spinners = document.getElementById('js__pimple__matches_spinners-div');
    table.classList.remove("d-none");
    spinners.classList.add("d-none");

    data.forEach(function (item, key) {
        let row = table.insertRow(key);
        let cellName = row.insertCell(0);
        let cellLink = row.insertCell(1);
        let cellBtn = row.insertCell(2);
        let btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('btn', 'btn-secondary', 'btn-sm', 'js__copy_btn');
        btn.dataset.copy = item.acestream;
        btn.innerText = 'Copy';
        btn.addEventListener('click', copyToClipboard.click);


        cellName.innerHTML = item.name;
        cellLink.innerHTML = item.acestream;
        cellBtn.append(btn);
    });
};

const init = () => {
    getData().then(function (data) {
        addDataToTable(data);
    });
};

init();