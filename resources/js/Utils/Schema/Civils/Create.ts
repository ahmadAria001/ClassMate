import { z } from "zod";

export const createSchema = z.object({
    "nik": z.coerce.string({
        required_error: "NIK Tidak Boleh Kosong"
    }).min(1).max(16),
    "fullName": z.coerce.string({
        required_error: "Nama Lengkap Tidak Boleeh Kosong"
    }).min(1),
    "birthplace": z.coerce.string({
        required_error: "Tempat Lahir Tidak Boleeh Kosong"

    }).min(1),
    "birthdate": z.coerce.string({
        required_error: "Tanggal Lahir Tidak Boleeh Kosong"

    }).min(1).transform((str) => {
        const formatted = str.replace('-', '/')
        return new Date(formatted).getSeconds()
    }),
    "nkk": z.coerce.string(
        {
            required_error: "NKK Tidak Boleeh Kosong"

        }
    ).min(1),
    "residentstatus": z.coerce.string({
        required_error: "Status Penduduk Tidak Boleeh Kosong"

    }).min(1),
    "rt_id": z.coerce.number({
        required_error: "RT Tidak Boleeh Kosong"

    }).min(1),
    "address": z.coerce.string({
        required_error: "Alamat Tidak Boleeh Kosong"

    }).min(1),
    "status": z.coerce.string({
        required_error: "Status Tidak Boleeh Kosong"

    }).min(1),
    "phone": z.coerce.string({
        required_error: "Nomor HP Tidak Boleeh Kosong"

    }).min(1).max(20),
    "religion": z.coerce.string({
        required_error: "Agama Tidak Boleeh Kosong"

    }).min(1),
    "job": z.coerce.string({
        required_error: "Pekerjaan Tidak Boleeh Kosong"

    }).min(1),
    "isFamilyHead": z.coerce.boolean(),
});

export type CreateSchema = z.infer<typeof createSchema>;
