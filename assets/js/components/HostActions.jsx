import React from "react";
import {CSVLink} from "react-csv";

export default function HostActions({hosts}) {
    const CSVHeaders = [
        { label: "Code Machine ", key: "name" },
        { label: "Libellé Machine", key: "name" },
        { label: "Nom DNS", key: "dns" },
        { label: "OS", key: "os" },
        { label: "CPU", key: "cpu" },
        { label: "RAM", key: "memory" },
        { label: "Adresse IP", key: "ip" },
        { label: "Type", key: "type" },
    ];

    const updatedHosts = [...hosts]
    updatedHosts.map(host => {
        host.type = host.model === 'vmware' ? 'VM' : 'Physical'
    })

    return (
        <div className={"my-12 flex gap-4"}>
            <a href="https://127.0.0.1:8000/save-to-db" className={"btn-action"}>Refresh database</a>
            <CSVLink
                data={updatedHosts}
                headers={CSVHeaders}
                filename={"export_serverscd50_" + new Date().toLocaleDateString() + ".csv"}
                className="btn-action"
                target="_blank"
            >
                Export to CSV
            </CSVLink>
        </div>
    )
}