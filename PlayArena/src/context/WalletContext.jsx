import { createContext, useState } from "react";

export const WalletContext = createContext();

function WalletProvider({ children }) {
  const [walletBalance, setWalletBalance] = useState(0);
  const [nxlCredits, setNxlCredits] = useState(50);

  const addCredits = (credits) => {
    setNxlCredits(nxlCredits + credits);
  };

  const redeemCredits = (credits) => {
    if (nxlCredits >= credits) {
      setNxlCredits(nxlCredits - credits);
    }
  };

  return (
    <WalletContext.Provider
      value={{
        walletBalance,
        nxlCredits,
        addCredits,
        redeemCredits,
      }}
    >
      {children}
    </WalletContext.Provider>
  );
}

export default WalletProvider;