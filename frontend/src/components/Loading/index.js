import { CircularProgress } from "@mui/material";
import React from "react";

const Loading = () => {
    return (
        <div style={{
            display: 'flex',
            justifyContent: 'center',
            width: '100%',
            height: '100vh',
        }}>
            <CircularProgress sx={{ mt: 40 }}/>
        </div>
    )
}

export default Loading;