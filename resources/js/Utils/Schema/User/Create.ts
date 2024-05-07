import { z } from 'zod'

export const createSchema = z.object({
    password: z.coerce.string().min(8),
    confirm_pass: z.coerce.string().min(8),
    nik: z.coerce.number().min(1),
    tos: z.coerce.boolean(),
}).superRefine(({ confirm_pass, password, tos }, ctx) => {
    if (confirm_pass !== password) {
        ctx.addIssue({
            code: "custom",
            message: "Password harus sama",
            path: ["confirm_pass"]
        })
    }

    if (!tos)
        ctx.addIssue({
            code: "custom",
            message: "Anda harus menerima syarat dan ketentuan",
            path: ['tos']
        })
})

export type CreateSchema = z.infer<typeof createSchema>;

