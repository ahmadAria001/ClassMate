import { z } from "zod";
export const CraeteCivilian = z.object({
    nik: z.coerce.string().max(16),
    fullName: z.coerce.string(),
    birthplace: z.coerce.string(),
    birthdate: z.coerce.string().transform((str) => new Date(str).getSeconds()),
    residentstatus: z.coerce.string(),
    family_id: z.coerce.number().int(),
});
export const UpdateCivilian = z.object({
    id: z.coerce.number().int(),
    nik: z.coerce.string().max(16),
    fullName: z.coerce.string(),
    birthplace: z.coerce.string(),
    birthdate: z.coerce.string().transform((str) => new Date(str).getSeconds()),
    residentstatus: z.coerce.string(),
    family_id: z.coerce.number().int(),
});
export const DeleteCivilian = z.object({
    id: z.coerce.number().int(),
});
export const CreateFamily = z.object({
    nkk: z.coerce.string().max(16),
    residentstatus: z.coerce.string(),
    rt_id: z.coerce.number().int(),
});
export const UpdateFamily = z.object({
    id: z.coerce.number().int(),
    nkk: z.coerce.string().max(16),
    residentstatus: z.coerce.string(),
    rt_id: z.coerce.number().int(),
});
export const DeleteFamily = z.object({
    id: z.coerce.number().int(),
});
export const CivilianRequest = z.object({
    nik: z.coerce.string().max(16),
    fullName: z.coerce.string(),
    birthplace: z.coerce.string(),
    birthdate: z.coerce.number().int(),
    residentstatus: z.coerce.string(),
    family_id: z.coerce.number().int(),
});
export const UpdateDues = z.object({
    id: z.coerce.number().int(),
    typeDues: z.coerce.string(),
    description: z.coerce.string(),
    amt_dues: z.coerce.number().int(),
    amt_fund: z.coerce.number().int(),
    status: z.coerce.boolean(),
    rt_id: z.coerce.number().int(),
});
export const DeleteDues = z.object({
    id: z.coerce.number().int(),
});
export const DeleteCiviliant = z.object({
    id: z.coerce.number().int(),
});
export const CreateDues = z.object({
    typeDues: z.coerce.string(),
    description: z.coerce.string(),
    amt_dues: z.coerce.number().int(),
    amt_fund: z.coerce.number().int(),
    status: z.coerce.boolean(),
    rt_id: z.coerce.number().int(),
});
export const Update = z.object({
    id: z.coerce.number().int(),
    request_by: z.coerce.number().int(),
    desc: z.coerce.string(),
});
export const Delete = z.object({
    id: z.coerce.number().int(),
});
export const Create = z.object({
    request_by: z.coerce.number().int(),
    desc: z.coerce.string(),
});
export const Login = z.object({
    username: z.coerce.string(),
    password: z.coerce.string(),
});
export const CreateRT = z.object({
    leader_id: z.coerce.number(),
});
export const UpdateRT = z.object({
    id: z.coerce.number(),
    leader_id: z.coerce.number(),
});
export const DeleteRT = z.object({
    id: z.coerce.number(),
});
