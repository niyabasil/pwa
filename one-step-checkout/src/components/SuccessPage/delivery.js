import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, number } from 'prop-types';
import defaultClasses from './successPage.module.css';
import { FormattedMessage, useIntl } from 'react-intl';
import moment from 'moment';

const Delivery = props => {
    const { data } = props;
    const classes = useStyle(defaultClasses, props.classes);
    const { locale } = useIntl();
    const formatL = moment.localeData(locale).longDateFormat('L');

    if (!data || !data.date) {
        return null;
    }

    const { date, comment, time } = data;

    const timeLine =
        time && time.value ? (
            <span>
                <FormattedMessage
                    id={'amOsc.deliveryTime'}
                    defaultMessage={'Delivery Time: {time}'}
                    values={{ time: time.displayValue }}
                />
            </span>
        ) : null;

    const commentLine = comment ? (
        <span>
            <FormattedMessage
                id={'amOsc.deliveryComment'}
                defaultMessage={'Delivery Comment: {comment}'}
                values={{ comment }}
            />
        </span>
    ) : null;

    return (
        <div className={classes.section}>
            <div className={classes.sectionTitle}>
                <FormattedMessage
                    id={'amOsc.delivery'}
                    defaultMessage={'Delivery:'}
                />
            </div>
            <div className={classes.sectionContent}>
                <span>
                    <FormattedMessage
                        id={'amOsc.deliveryDate'}
                        defaultMessage={'Delivery Date: {date}'}
                        values={{
                            date: moment(date, 'YYYY-MM-DD').format(formatL)
                        }}
                    />
                </span>
                {timeLine}
                {commentLine}
            </div>
        </div>
    );
};

Delivery.propTypes = {
    data: shape({
        date: string,
        time: shape({
            value: number,
            displaYValue: string
        }),
        comment: string
    }),
    classes: shape({
        root: string
    })
};

export default Delivery;
