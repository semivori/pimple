// Try edit message


const getData = async () => {
    const res = await fetch('pimple.php');
    return await res.json();
};

const copyToClipboard = {
    click: function (e) {
        const input = document.createElement('input');
        const btn = e.currentTarget;
        const btns = document.getElementsByClassName('js__pimple__matches_copy_link-btn');
        input.value = btn.dataset.copy;
        document.body.append(input);
        input.select();
        document.execCommand("copy");
        input.remove();

        for (let i = 0; i < btns.length; i++) {
            const item = btns.item(i);
            item.innerHTML = 'Copy';
        }
        btn.innerHTML = 'Copied';
    }
};

const addDataToTable = (data) => {
    const table = document.getElementById('js__pimple__matches-table');
    const spinners = document.getElementById('js__pimple__matches_spinners-div');
    table.classList.remove("d-none");
    spinners.classList.add("d-none");

    data.forEach(function (item, key) {
        const row = table.insertRow(key);
        const cellName = row.insertCell(0);
        const cellLink = row.insertCell(1);
        const cellBtn = row.insertCell(2);

        cellName.innerHTML = `${item.time}, ${item.name}, ${item.channel}`;

        if (item.acestream) {
            const btnCopy = document.createElement('button');
            btnCopy.type = 'button';
            btnCopy.classList.add('btn', 'btn-secondary', 'btn-sm', 'js__copy_btn', 'js__pimple__matches_copy_link-btn');
            btnCopy.dataset.copy = item.acestream;
            btnCopy.innerText = 'Copy';
            btnCopy.addEventListener('click', copyToClipboard.click);
            cellBtn.append(btnCopy);

            const link = document.createElement('a');
            link.href = item.acestream;
            link.innerText = 'Open';
            cellLink.append(link);
        }
    });
};

const init = () => {
    getData().then(function (data) {
        addDataToTable(data);
    });
};

init();