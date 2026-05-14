import React, { useEffect, useState } from 'react';
import { useMutation } from '@apollo/client';
import gql from 'graphql-tag';
import { useLocation, Link , Redirect } from 'react-router-dom';
import LoadingIndicator from "@magento/venia-ui/lib/components/LoadingIndicator";
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import defaultClasses from './eway.css';

const FINALIZE_PAYMENT = gql`
  mutation finalizePayment($cartId: String!, $accessCode: String!) {
    ewayRedirectSuccess(cart_id: $cartId, access_code: $accessCode) {
      success
      message
      order_id
      order_data {
        shipping_address {
          first_name
          last_name
          street
          city
          region
          postcode
          country
          phone
        }
        billing_address {
          first_name
          last_name
          street
          city
          region
          postcode
          country
          email
        }
        shipping_method {
          carrier_code
          carrier_title
          method_code
          method_title
          shipping_cost
        }
        payment_method {
          method_code
          method_title
        }
        eway_order_total {
          subtotal
          shipping_amount
          tax_amount
          discount_amount
          grand_total
          currency
        }
        items {
          item_id
          product_name
          sku
          quantity_ordered
          price
          row_total
        }
      }
    }
  }
`;

const Eway = () => {
    const location = useLocation();
    const [{ cartId }] = useCartContext();
    const [accessCode, setAccessCode] = useState(null);
    const [finalizePayment, { loading, error, data }] = useMutation(FINALIZE_PAYMENT);
    const [orderId, setOrderId] = useState(null);
    const [orderSuccess, setOrderSuccess] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [orderData, setOrderData] = useState(null);
    const [redirect, setRedirect] = useState(false);
    const [countdown, setCountdown] = useState(5);
    useEffect(() => {
        const url = new URL(window.location.href);
        const accessCodeFromUrl = url.searchParams.get('AccessCode');
        setAccessCode(accessCodeFromUrl);
    }, [location]);

    useEffect(() => {
        if (cartId && accessCode) {
            finalizePayment({
                variables: {
                    cartId,
                    accessCode: accessCode,
                },
            });
        }
    }, [cartId, accessCode, finalizePayment]);

    useEffect(() => {
        if (data) {
            const { success, message, order_id, order_data } = data.ewayRedirectSuccess;
            if (success) {
                setOrderId(order_id);
                setOrderSuccess(true);
                setOrderData(order_data);
                sessionStorage.setItem('order_id', order_id);
                const timer = setInterval(() => {
                    setCountdown(prev => {
                        if (prev <= 1) {
                            setRedirect(true);
                            clearInterval(timer);
                            return 0;
                        }
                        return prev - 1;
                    });
                }, 1000);
            } else {
                setOrderSuccess(false);
                setErrorMessage(message || 'An error occurred while processing your payment.');
            }
        }
    }, [data]);

    if (loading) {
        return (
            <LoadingIndicator>
                <div><p>Fetching your order...</p></div>
            </LoadingIndicator>
        );
    }

    if (error) {
        return <div><p>Error: {error.message}</p></div>;
    }

    if (redirect) {
        return <Redirect to="/" />;
    }

    const renderOrderData = () => {
        if (!orderData) return null;

        const { shipping_address, billing_address, shipping_method, payment_method, eway_order_total, items } = orderData;

        const totalItems = items.reduce((total, item) => total + item.quantity_ordered, 0);

        return (
            <div style={orderDataStyle}>
                {/* Shipping and Billing Address in the same row */}
                <div style={containerStyle}>
                    {/* Shipping Address */}
                    <div style={columnStyle}>
                        <h3 style={sectionTitleStyle}>Shipping Address</h3>
                        <div style={addressStyle}>
                            <p>{`${shipping_address.first_name} ${shipping_address.last_name}`}</p>
                            <p>{shipping_address.street}</p>
                            <p>{`${shipping_address.city}, ${shipping_address.region} ${shipping_address.postcode}`}</p>
                            <p>{shipping_address.country}</p>
                            <p>Phone: {shipping_address.phone}</p>
                        </div>
                    </div>

                    {/* Billing Address */}
                    <div style={columnStyle}>
                        <h3 style={sectionTitleStyle}>Billing Information</h3>
                        <div style={addressStyle}>
                            <p>{`${billing_address.first_name} ${billing_address.last_name}`}</p>
                            <p>{billing_address.street}</p>
                            <p>{`${billing_address.city}, ${billing_address.region} ${billing_address.postcode}`}</p>
                            <p>{billing_address.country}</p>
                            <p>Email: {billing_address.email}</p>
                        </div>
                    </div>
                </div>

                <div style={payContainerStyle}>
                    {/* Shipping Method */}
                    <div style={addressStyle}>
                        <h3 style={sectionTitleStyle}>Shipping Method</h3>
                        <div style={orderDetailsStyle}>
                            <p>{shipping_method.method_title} - ${shipping_method.shipping_cost.toFixed(2)}</p>
                        </div>
                    </div>

                    {/* Payment Method */}
                    <div style={addressStyle}>
                        <h3 style={sectionTitleStyle}>Payment Method</h3>
                        <div style={orderDetailsStyle}>
                            <p>{payment_method.method_title}</p>
                        </div>
                    </div>
                </div>

                {/* Order Items */}
                <h3 style={sectionTitleStyle}>Order Items ({totalItems} item{totalItems !== 1 ? 's' : ''})</h3>
                <table style={tableStyle}>
                    <thead>
                        <tr>
                            <th style={tableHeaderStyle}>Product Name</th>
                            <th style={tableHeaderStyle}>SKU</th>
                            <th style={tableHeaderStyle}>Quantity</th>
                            <th style={tableHeaderStyle}>Price</th>
                            <th style={tableHeaderStyle}>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {items.map(item => (
                            <tr key={item.item_id} style={tableRowStyle}>
                                <td style={tableCellStyle}>{item.product_name}</td>
                                <td style={tableCellStyle}>{item.sku}</td>
                                <td style={tableCellStyle}>{item.quantity_ordered}</td>
                                <td style={tableCellStyle}>${item.price.toFixed(2)}</td>
                                <td style={tableCellStyle}>${item.row_total.toFixed(2)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {/* Order Total (Moved after Order Items) */}
                <div style={orderDetailsStyle}>
                    <p>Subtotal: ${eway_order_total.subtotal.toFixed(2)}</p>
                    <p>Shipping: ${eway_order_total.shipping_amount.toFixed(2)}</p>
                    <p>Tax: ${eway_order_total.tax_amount.toFixed(2)}</p>
                    <p>Discount: ${eway_order_total.discount_amount.toFixed(2)}</p>
                    <p><strong>Grand Total: ${eway_order_total.grand_total.toFixed(2)}</strong></p>
                    <p>Currency: {eway_order_total.currency}</p>
                </div>
            </div>
        );
    };

    return (
        <div>
            {orderSuccess && (
                <div style={messageStyle}>
                    <p>Thank you for your order! Redirecting to the homepage in {countdown} seconds...</p>
                </div>
            )}
            {orderSuccess ? (
                <>
                    <h1 style={orderStyle}>Thank you for your order!</h1>
                    <h2 style={orderStyle}>
                        {orderId ? (
                            <>Your Order ID: <Link to="/order-history">00000{orderId}</Link></>
                        ) : (
                            "Order ID not found."
                        )}
                    </h2>
                    {renderOrderData()}
                </>
            ) : (
                <div><p>Error: {errorMessage}</p></div>
            )}
        </div>
    );
};

const orderStyle = { margin: "1rem", textAlign: 'center', fontSize: '24px', color: '#333' };
const orderDataStyle = { margin: "2rem auto", padding: "1rem", border: "1px solid #ddd", borderRadius: "8px", maxWidth: "800px" };
const sectionTitleStyle = { fontSize: "20px", marginBottom: "1rem", color: "#555", fontWeight: 'bold' };
const addressStyle = { marginBottom: "2rem", lineHeight: "1.5", color: "#333" };
const orderDetailsStyle = { marginBottom: "1.5rem", color: "#333", lineHeight: "1.5" ,textAlign: "right", marginLeft: "auto", maxWidth: "300px" };
const tableStyle = { width: '100%', borderCollapse: 'collapse', marginTop: '1rem' };
const tableHeaderStyle = { padding: '12px', backgroundColor: '#f4f4f4', border: '1px solid #ddd', textAlign: 'left', fontWeight: 'bold' };
const tableRowStyle = { borderBottom: '1px solid #ddd', padding: '12px', backgroundColor: '#fafafa' };
const tableCellStyle = { padding: '12px 15px', textAlign: 'left', verticalAlign: 'middle' };
const containerStyle = { display: 'flex', justifyContent: 'space-between', gap: '1rem', marginBottom: '2rem'};
const payContainerStyle = { display: 'flex',  gap: '10rem', marginBottom: '2rem'};
const columnStyle = { flex: 1};
const messageStyle = { backgroundColor: '#f0f8ff', padding: '1rem', margin: '1rem 0', textAlign: 'center', color: '#333', fontSize: '16px', border: '1px solid #ddd', borderRadius: '8px' };
export default Eway;
