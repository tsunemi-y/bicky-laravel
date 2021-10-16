import React, {useState} from 'react';
import { Link } from 'react-router-dom';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEnvelope, faAngleDown } from "@fortawesome/free-solid-svg-icons";


const SideMail: React.FC = () => {
    const [toggle, setToggle] = useState<Boolean>(false);

    return (
        <>
            <li className="mt-3">
                <span className="inline-block w-24">
                    <FontAwesomeIcon className="mr-1 text-white" icon={faEnvelope} />  
                    メール送信
                </span>
                <FontAwesomeIcon className="ml-3 text-white" icon={faAngleDown} onClick={e => setToggle(!toggle)}/>
                <ul className={toggle ? "transition duration-700 h-8" : "h-0 overflow-hidden"}>
                    <li>
                        <Link to="/admin/receipt">領収書</Link>
                    </li>
                </ul>
            </li>
        </>
    );
}
    
export default SideMail;
