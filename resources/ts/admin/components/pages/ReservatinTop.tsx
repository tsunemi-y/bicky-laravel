import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import Calendar from 'react-calendar';
import 'react-calendar/dist/Calendar.css';
import dayjs from 'dayjs';
import Loading from '../parts/Loading';
import _Modal from '../parts/Modal';

interface Props {
    title: string
}
interface RData {
    name: string
    reservation_time: string
}

interface TileContent {
    date: Date,
    view: string
  }

const rData = {
    name: '',
    reservation_time: ''
}

const ReservatinTop: React.FC<Props> = (props) => {
    const { title } = props;
    const [avaTimes, setAvaTimes] = useState<any>(rData);
    const [holidays, setHolidays] = useState<any>([]);
    const [date, setDate] = useState<Date>(new Date());
    const [datetime, setDatetime] = useState<any>('');
    const [isBulkWeekend, setisBulkWeekend] = useState<boolean>(false);
    const [isBulkDay, setisBulkDay] = useState<boolean>(false);
    const [isBulkMonth, setisBulkMonth] = useState<boolean>(false);
    const [deletedTargetAvaTimes, setDeletedTargetAvaTimes] = useState<any>([]);
    const [isShownModal, setIsShownModal] = useState<boolean>(false);
    const [loadingDispFlag, setLoadingDispFlag] = useState<Boolean>(false);
    const [reservations, setReservations] = useState<any>(rData);
    
    const fetchReservations = async () => {
        try {
            setLoadingDispFlag(true);
            const response = await axios.get(`/api/admin/reservation`);
            setReservations(response.data.reservations);
            setAvaTimes(response.data.avaTimes);
            setHolidays(response.data.holidays);
            setLoadingDispFlag(false);
        } catch (err) {
            alert('エラーです。やり直してください。');
            setLoadingDispFlag(false);
        }
    }

    useEffect(() => {
        fetchReservations();
    },[]);

    const getTileContent = ({ date, view }: TileContent): any  => {
        if (view !== 'month') return null;
        const formatDate: string | never = dayjs(date).format('YYYY-MM-DD');

        const targetReservations = reservations[formatDate];
        const targetAvaTimes = avaTimes[formatDate];
        
        let avaTimeList: any = [];
        for(let i in targetAvaTimes){
            avaTimeList.push(<p key={ i }>{ targetAvaTimes && targetAvaTimes[i] }</p>);
        }

        let reservationList: any = [];
        for(let i in targetReservations){
            reservationList.push(<p key={ i }>{ targetReservations && `${targetReservations[i].reservationName}: ${targetReservations[i].reservationTime}` }</p>);
        }

        if (reservationList.length && avaTimeList.length) {
            return (
                <>
                    <div className="text-xs text-left">
                        { reservationList }
                    </div>

                    <div className="text-xs text-left">
                        { avaTimeList }
                    </div>
                </>
            )

        } else if (reservationList.length) {
            return (
                <div className="text-xs text-left">
                    { reservationList }
                </div>
            )

        } else if (avaTimeList.length) {
            return (
                <div className="text-xs text-left">
                    { avaTimeList }
                </div>
            )
        }
    }

    const getTileClassName= ({ date, view }: TileContent): any  => {
        if (!holidays) return null;
        if (view !== 'month') return null;

        const formatDate: string | never = dayjs(date).format('YYYY-MM-DD');
        if (holidays.includes(formatDate)) return 'react-calendar__month-view__days__day--weekend';
    }

    const onChangeDatetime = (event: React.ChangeEvent<HTMLInputElement>): void =>  {
        const value: string = event.target.value;
        setDatetime(value);
    }

    const onChangeisBulkWeekend = (event: React.ChangeEvent<HTMLInputElement>): void =>  {
        const value: boolean = JSON.parse(event.target.value);
        setisBulkWeekend(value);
    }

    const onChangeisBulkDay = (event: React.ChangeEvent<HTMLInputElement>): void =>  {
        const value: boolean = JSON.parse(event.target.value);
        setisBulkDay(value);
    }

    const onChangeisBulkMonth = (event: React.ChangeEvent<HTMLInputElement>): void =>  {
        const value: boolean = JSON.parse(event.target.value);
        setisBulkMonth(value);
    }

    // 予約可能テーブルに予約可能日時登録
    const createDatetime = async () => {
        try {
            const replacedDatetime = datetime.replace('T', ' '); // inputのdatetimeは時間表記の前にTが入るのでそれを削除
            const result: boolean = confirm(`${replacedDatetime}を予約可能日時に設定します。よろしいですか？`);
            if (result === false) return;
            const data = {
                datetime: replacedDatetime,
                isBulkDay,
                isBulkMonth,
                isBulkWeekend
            }
            setLoadingDispFlag(true);
            await axios.post(`/api/admin/saveReservation`, data);
            fetchReservations();
            alert('登録に成功しました。');
        } catch (err) {
            alert('登録に失敗しました。やり直してください。');
            setLoadingDispFlag(false);
        }
      }

      // 日付押下時、モーダル内に押下した日付に紐づく時間一覧を表示させる。
    const handleOnClickDay = (e: any) => {
        const selectedDay: any = dayjs(e).format('YYYY-MM-DD')
        const targetAvaTimes: any = avaTimes[selectedDay];
        
        let avaTimeList: any = [];
        for(var i in targetAvaTimes){
            avaTimeList.push(
                <button 
                    className="bg-red-500 block mt-3 p-1 rounded text-center text-white w-20" 
                    key={i} 
                    data-date={selectedDay} 
                    onClick={(e: any) => deleteDatetime(selectedDay, e.target.innerText ) }
                >
                    {targetAvaTimes[i]}
                </button>
            );
        }
        
        setDeletedTargetAvaTimes(avaTimeList);
        setIsShownModal(true);
    }

    const deleteDatetime = async (date: String, time: String) => {
        try {
            const result: boolean = confirm(`${date} ${time}を削除します。よろしいですか？`);
            if (result === false) return;
            setLoadingDispFlag(true);
            const data = {
                date,
                time
            };
            await axios.post(`/api/admin/deleteReservation`, data);
            fetchReservations();
            alert('削除に成功しました。');
            setIsShownModal(false);
        } catch (err) {
            alert('削除に失敗しました。やり直してください。');
            setLoadingDispFlag(false);
            setIsShownModal(false);
        }
    }

    // モーダル閉じる
    const closeModal = (): void => setIsShownModal(false);
    
return (
        <>
            <h1　className="font-bold text-left text-2xl">{title}</h1>
            <div className="mt-3">
                <div className="mb-2">
                    <label className="font-bold mr-3" htmlFor="avaDatetime">利用可能日時追加</label>
                    <input value={ datetime } onChange={onChangeDatetime} className="border-2 border-black border-solid p-0.5 rounded" type="datetime-local" id="avaDatetime"/>
                </div>

                <div>
                    <p className="font-bold">■一括登録</p>
                    <div>
                        <label className="font-bold w-16 inline-block">土日</label>
                        <label htmlFor="notisBulkWeekend"><input type="radio" name="isBulkWeekend" value="false" onChange={onChangeisBulkWeekend} checked={isBulkWeekend === false} id="notisBulkWeekend"/>なし</label>
                        <label htmlFor="isBulkWeekend"><input type="radio" name="isBulkWeekend" value="true" onChange={onChangeisBulkWeekend} checked={isBulkWeekend === true} id="isBulkWeekend" className="ml-2" />あり</label>
                    </div>

                    <div>
                        <label className="font-bold w-16 inline-block">一ヶ月</label>
                        <label htmlFor="notisBulkMonth"><input type="radio" name="isBulkMonth" value="false" onChange={onChangeisBulkMonth} checked={isBulkMonth === false} id="notisBulkMonth"/>なし</label>
                        <label htmlFor="isBulkMonth"><input type="radio" name="isBulkMonth" value="true" onChange={onChangeisBulkMonth} checked={isBulkMonth === true} id="isBulkMonth" className="ml-2" />あり</label>
                    </div>

                    <div>
                        <label className="font-bold w-16 inline-block">一日</label>
                        <label htmlFor="notisBulkDay"><input type="radio" name="isBulkDay" value="false" onChange={onChangeisBulkDay} checked={isBulkDay === false} id="notisBulkDay"/>なし</label>
                        <label htmlFor="isBulkDay"><input type="radio" name="isBulkDay" value="true" onChange={onChangeisBulkDay} checked={isBulkDay === true} id="isBulkDay" className="ml-2" />あり</label>
                    </div>
                </div>

                <div className="bg-blue-900 mt-3 p-1 rounded text-center text-white w-20">
                    <button className="w-full" onClick={ createDatetime }>追加</button>
                </div>
                <Calendar
                    className={ ['mt-6', 'w-full'] }
                    locale='ja-JP'
                    value={date}
                    formatDay={(locale: any, date: Date) => dayjs(date).format('DD')}
                    tileContent={getTileContent}
                    tileClassName={getTileClassName}
                    onClickDay={handleOnClickDay}
                />
            </div>
            <_Modal 
                isShownModal={ isShownModal }
                title='削除する時間を選択してください。'
                body={ deletedTargetAvaTimes }
                closeModal={ closeModal }
            />
            {loadingDispFlag && <Loading />}
        </>
    );
}
export default ReservatinTop;

