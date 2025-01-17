import express, { Request, Response } from 'express'
import cors from 'cors'
import dotenv from 'dotenv'
import cookieParser from 'cookie-parser'
import multer from 'multer'
import { fileRouter } from './router/file.route.ts'
import router from './router/index.route.ts'
import { authRouter } from './router/auth.route.ts'
import { adminRouter } from './router/admin.route.ts'

dotenv.config()

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());
app.use(cors());
app.use(cookieParser());

app.get('/', (req: Request, res: Response) => {
  res.status(200).send({message: 'ok!'});
})

const formData = multer()

// route
app.use("/api/v1", fileRouter); // file router to stay first
app.use("/api/v1/auth", formData.none(), authRouter);
app.use("/api/v1/admin", formData.none(), adminRouter);
app.use("/api/v1", formData.none(), router);

app.listen(PORT, () => {
  console.log(`[server]: Server is running at port ${PORT}`);
});