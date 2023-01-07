import React, {useState, useEffect} from 'react';
import axios from 'axios';
import { useParams } from 'react-router-dom';
import Loading from '../parts/Loading';

interface Props {
    title: string
}

interface RData {
    parentName: string
    email: string
    fee: number | undefined
}

interface urlParam {
    id: string | undefined
}

const rData: RData = {
    parentName: '',
    email: '',
    fee: undefined,
}

const ReceiptSend: React.FC<Props> = (props) => {
    const { title } = props;

    const [user, setUser] = useState(rData);
    const [discountAmount, setDiscountAmount] = useState<number | undefined>(undefined);
    const [loadingDispFlag, setLoadingDispFlag] = useState<Boolean>(false);

    const { id }: urlParam = useParams();

    const onChangeFee = async (event: React.ChangeEvent<HTMLInputElement>) => {
        try {
            setLoadingDispFlag(true);
            const value: number = parseInt(event.target.value, 10);
            setUser({ ...user, fee: value });
            await axios.put(`/api/admin/updateFee/${id}`, {fee: value});
            setLoadingDispFlag(false);
        } catch {
            alert('料金の変更に失敗しました。')
            setLoadingDispFlag(false);
        }
    }

    // const hiddenDiscountAmmount = => {

    // }

    const sendReceipt = async () =>  {
        try {
            setLoadingDispFlag(true);
            const args = {
                name: user.parentName,
                email: user.email,
                fee: user.fee,
                discountAmount: discountAmount
            }
            console.log(args);
            await axios.post('/api/admin/sendReceipt', args);
            alert('領収書の送信に成功しました。');
            setLoadingDispFlag(false);
        }catch (err) {
            alert('エラーです。やり直してください。');
            setLoadingDispFlag(false);
        }
    }

    useEffect(() => {
        const fetchUsersById = async () => {
            try {
                setLoadingDispFlag(true);
                const response = await axios.get(`/api/admin/users?id=${id}`);
                setUser(response.data[0]);
                setLoadingDispFlag(false);
            } catch (err) {
                alert('エラーです。やり直してください。');
                setLoadingDispFlag(false);
            }
        }
        fetchUsersById();
    },[]);
    
    return (
        <>
            <h1　className="font-bold text-left text-2xl">{title}</h1>
            <div className="bg-white mt-3 p-4 w-3/4">
               <p><span className="block w-32">【氏名】</span>{user.parentName}</p>
               <p className="mt-3"><span className="block w-32">【メール】</span>{user.email}</p>
               <p className="mt-3"><span className="block w-32">【料金】</span><input className="border rounded" onBlur={onChangeFee} defaultValue={user.fee}/></p>
               {/* <p className="mt-3">
                <span className="block w-32">【クーポン割引】</span>
                <input type="radio" name="isDiscount" className="border rounded" defaultValue='1' checked/><label className="mr-2">適用しない</label>
                <input type="radio" name="isDiscount" className="border rounded" defaultValue='0' onClick={hiddenDiscountAmmount}/><label>適用する</label>
                </p> */}
               <p className="mt-3 hidden"><span className="block w-32">【クーポン割引金額】</span><input className="border rounded" defaultValue={discountAmount}/></p>
               <div className="bg-blue-900 mt-3 p-1 rounded text-center text-white w-20">
                    <button className="w-full" onClick={sendReceipt}>送信</button>
               </div>
            </div>
            {loadingDispFlag && <Loading />}
        </>
    );
}
export default ReceiptSend;

