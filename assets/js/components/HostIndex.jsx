import React, {useEffect, useState, useMemo} from "react";
import HostActions from "./HostActions";
import debounce from "lodash.debounce";

export default function HostIndex() {
    const [hosts, setHosts] = useState([])

    useEffect(() => {
        const response = fetch("https://127.0.0.1:8000/api/hosts")
        const JsonResponse = response.then(response => response.json())
        JsonResponse.then(data => setHosts(data['hydra:member']))
    }, [])

    const handleChangeSearch = (event) => {
        const search = event.target.value
        const response = fetch("https://127.0.0.1:8000/api/hosts?name=" + search)
        const JsonResponse = response.then(response => response.json())
        JsonResponse.then(data => setHosts(data['hydra:member']))
    }

    const debouncedHandleSearch = useMemo(() => debounce(handleChangeSearch, 300), []);

    return(
        <div>
            <HostActions hosts={hosts}/>
            <div className="search-host flex gap-4">
                <input type="text" placeholder={"Search host..."} onChange={event => debouncedHandleSearch(event)}/>
                <span>{hosts.length} host{hosts.length === 1 ? '' : 's'} found</span>
            </div>
            { hosts && hosts.length > 0 &&
                <div>
                    <div className="host-list">
                        <table>
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Dns</th>
                                <th>Ip</th>
                                <th>Os</th>
                                <th className={"pr-6 text-center"}>CPU / Cores</th>
                                <th className={""}>Memory</th>
                            </tr>
                            </thead>
                            <tbody>
                            {
                                hosts.map(host =>
                                    <tr key={host.id}>
                                        <td>
                                            <span className={host.powerstate === true ? 'green-state' : 'red-state'}></span>
                                            { host.name }
                                        </td>
                                        <td>{ host.dns }</td>
                                        <td>{ host.ip }</td>
                                        <td>{ host.os }</td>
                                        <td className={"text-center pr-6"}>{ host.cpu + " / " + host.cores }</td>
                                        <td className={"text-center"}>{ host.memory }</td>
                                    </tr>
                                )
                            }
                            </tbody>
                        </table>
                    </div>
                </div>
            }
        </div>
    )
}