import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, shape, string } from 'prop-types';
import defaultClasses from './successPage.module.css';
import { FormattedMessage } from 'react-intl';
import Icon from '@magento/venia-ui/lib/components/Icon';
import { ChevronDown, ChevronUp } from 'react-feather';
import { useGiftMessage } from '../../talons/SuccessPage/useGiftMessage';

const GiftMessage = props => {
    const { data, expandable } = props;
    const { isExpanded, toggleHandler } = useGiftMessage({ expandable });
    const classes = useStyle(defaultClasses, props.classes);

    if (!data) {
        return null;
    }
    const { sender, recipient, message } = data;
    const toggleIconSrc = isExpanded ? ChevronUp : ChevronDown;
    const contentToggleIcon = <Icon src={toggleIconSrc} size={16} />;

    const titleElement = !expandable ? (
        <div className={classes.sectionTitle}>
            <FormattedMessage
                id={'amOsc.giftMessageForOrderTitle'}
                defaultMessage={'Gift Message for This Order'}
            />
        </div>
    ) : (
        <button className={classes.toggleButton} onClick={toggleHandler}>
            <FormattedMessage
                id={'amOsc.giftMessageTitle'}
                defaultMessage={'Gift Message'}
            />
            {contentToggleIcon}
        </button>
    );

    return (
        <div className={classes.section}>
            {titleElement}

            {isExpanded && (
                <div className={classes.sectionContent}>
                    <span>
                        <FormattedMessage
                            id={'amOsc.from'}
                            defaultMessage={'From: {sender}'}
                            values={{ sender }}
                        />
                    </span>
                    <span>
                        <FormattedMessage
                            id={'amOsc.to'}
                            defaultMessage={'To: {recipient}'}
                            values={{ recipient }}
                        />
                    </span>
                    <span>
                        <FormattedMessage
                            id={'amOsc.message'}
                            defaultMessage={'Message: {message}'}
                            values={{ message }}
                        />
                    </span>
                </div>
            )}
        </div>
    );
};

GiftMessage.propTypes = {
    data: shape({
        from: string,
        to: string,
        message: string
    }),
    expandable: bool,
    classes: shape({
        root: string
    })
};

export default GiftMessage;
